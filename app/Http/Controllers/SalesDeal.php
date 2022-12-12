<?php

namespace App\Http\Controllers;

use App\SalesDeal as SalesDealModel;
use App\SpecialRateDeal;
use App\SalesDealFile;
use App\Account;
use App\Branch;
use App\CurrencyPair;
use App\Currency;
use App\ClosingRate;
use App\Group;
use App\Market;
use App\OtherLhbuRemarksKind;
use App\Threshold;
use App\Modification;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SalesDeal extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(SalesDealModel::class, 'salesDeal');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Route::current()->named('sales-top-ten-obox.index')) {
            $this->authorize('update', new SalesDealModel);
        }

        $regions;

		try {
            $regions = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('NamaRegion as region')
                ->where('Company name', 'not like', '%'.strtoupper('(tutup)'))
                ->whereNotNull('NamaRegion')
                ->get();

        } catch (\Exception $e) {
            $regions = Branch::select('region')
                ->whereNotNull('region')
                ->get();
        }

        $regions = $regions->map( function($item) {
                if ($item instanceof Branch) {
                    $item = $item->toArray();
                } else {
                    $item = ((array) $item);
                }

                return ((object) array_map('htmlspecialchars_decode', $item));
            });

		$market = Market::whereDate('closing_at', '<=', Carbon::today()->toDateString())
            ->latest('closing_at')
            ->first();

		$marketTrashed = null;

        if ($market && $market->closing_at->lessThanOrEqualTo($market->opening_at)) {
            $marketTrashed = Market::onlyTrashed()
                ->whereDate('closing_at', $market->closing_at->toDateString())
                ->whereRaw('closing_at > opening_at')
                ->latest('updated_at')
                ->first();
        }

        $lhbuRemarksCode = Group::where('group', 'lhbu_remarks_code')
            ->get(['name_id as id', 'name'])
            ->map( function($item) {
                $item->text = substr('0'.((string) $item->id), -2);
                $item->text .= ' ';
                $item->text .= ucfirst($item->name);

                return $item;
            });

        $lhbuRemarksKind = Group::where('group', 'lhbu_remarks_kind')
            ->get(['name_id as id', 'name'])
            ->map( function($item) {
                $item->text = substr('00'.((string) $item->id), -3);
                $item->text .= ' ';
                $item->text .= ucfirst($item->name);

                return $item;
            });

        return view('sales-deal.index', [
			'market' => $market,
			'marketTrashed' => $marketTrashed,
			'regions' => $regions,
			'lhbuRemarksCode' => $lhbuRemarksCode,
			'lhbuRemarksKind' => $lhbuRemarksKind,
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'base-primary-code' => [
                'required',
                Rule::exists((new Currency)->getTable(), 'primary_code')
                ->where(function ($query) use($request) {
                    $query->where('id', (
                        ClosingRate::where('currency_id', (
                            Currency::whereNull('secondary_code')
                            ->firstOrNew(
                                ['primary_code' => $request->input('base-primary-code')],
                                ['id' => null]
                            )
                            ->id
                        ))->firstOrNew(
                            [
                                'created_at' => Market::whereDate('closing_at', '<', Carbon::today()->toDateString())
                                    ->latest('closing_at')
                                    ->firstOr( function() {
                                        $market = Market::select('closing_at')
                                            ->latest('closing_at')
                                            ->first();

                                        while ($market->closing_at->isWeekend()) {
                                            $market->closing_at = $market->closing_at->subDay();
                                        }

                                        return $market->fill(['closing_at' => $market->closing_at->toDateString()]);
                                    })
                                    ->closing_at
                                    ->toDateString()
                            ],
                            [
                                'currency_id' => null
                            ]
                        )
                        ->currency_id
                    ));
                }),
            ],
            'sismontavar-option-id' => [
                'required',
                'exists:App\SismontavarOption,id',
            ],
        ]);

        $baseCurrencyClosingRateId = ClosingRate::firstWhere([
                'currency_id' => Currency::whereNull('secondary_code')
                    ->firstOrNew(
                        ['primary_code' => $request->input('base-primary-code')],
                        ['id' => null]
                    )
                    ->id,

                'created_at' => Market::whereDate('closing_at', '<', Carbon::today()->toDateString())
                    ->latest('closing_at')
                    ->firstOr( function() {
                        $market = Market::select('closing_at')
                            ->latest('closing_at')
                            ->first();

                        while ($market->closing_at->isWeekend()) {
                            $market->closing_at = $market->closing_at->subDay();
                        }

                        return $market->fill(['closing_at' => $market->closing_at->toDateString()]);
                    })
                    ->closing_at
                    ->toDateString(),
            ])
            ->id;

        $usdEquivalent = ($request->input('amount') ?: 0);

        if (Currency::where('primary_code', $request->input('base-primary-code'))->first()->id != 1) {
            $usdEquivalent = new SalesDeal([
                    'base_currency_closing_rate_id' => $baseCurrencyClosingRateId,
                    'amount' => ($request->input('amount') ?: 0)
                ]);

            $usdEquivalent = $usdEquivalent->usd_equivalent;
        }

        $request->validate([
            'sales-limit' => [
                'required',
                'gt:'.$usdEquivalent,
            ],
        ]);

        if (Str::of($request->input('buy-sell'))->lower()->after('bank')->trim()->exactly('sell') && !$request->filled('counter-primary-code')) {
            $request->validate([
                'threshold',
                'gt:'.$usdEquivalent,
            ]);
        }

        try {
			$decrypted = Crypt::decryptString($request->input('encrypted-query-string'));

            parse_str($decrypted, $parsed);
            $request->request->set('currency_id', $parsed['id']);
            $request->request->set('interoffice_rate', $parsed[Str::of($request->input('buy-sell'))->lower()->after('bank')->trim().'ing_rate']);
            $request->request->set(
                'base_currency_rate',
                $parsed['base_currency_'.Str::of($request->input('buy-sell'))->lower()->after('bank')->trim().'ing_rate']
            );
            $request->request->set('csrf_token', $parsed['csrf_token']);

		} catch (DecryptException $e) {
			$request->request->set('currency_id', null);
			$request->request->set('interoffice_rate', null);
			$request->request->set('base_currency_rate', null);
			$request->request->set('csrf_token', null);
		}

		if ($request->route()->named('sales-fx.store')) {
			$request->validate([
				'currency_id' => [
					'required',
					Rule::exists((new CurrencyPair)->getTable(), 'id')->where(function ($query) {
						$query->where('belongs_to_sales', true);
					})
				],
                'csrf_token' => [
					'required',
                    Rule::in([
                        $request->session()->token(),
                        $request->user()->token->api_token,
                    ]),
				],
			]);

		} else {
			$request->request->set('currency_id', (
				CurrencyPair::where( function($query) use($request) {
                    $query->whereHas('baseCurrency', function($query) use($request) {
                        $query->where(
                            DB::raw('CASE WHEN secondary_code IS NULL THEN primary_code ELSE secondary_code END'),
                            $request->input('base-secondary-code')
                        );
                    });

                    if ($request->filled('counter-secondary-code')) {
                        $query->whereHas('counterCurrency', function($query) use($request) {
                            $query->where(
                                DB::raw('CASE WHEN secondary_code IS NULL THEN primary_code ELSE secondary_code END'),
                                $request->input('counter-secondary-code')
                            );
                        });

                    } else {
                        $query->whereNull('counter_currency_id');
                    }
                })
				->first()
				->id
			));

            $request->request->set('interoffice_rate', $request->input('interoffice-rate'));
		}

		$account = Account::firstOrCreate(
				[
					'number' => $request->input('account-number'),
					'cif' => $request->input('account-cif'),
					'name' => trim($request->input('account-name'))
				],
				[
					'user_id' => Auth::id(),
				]
			);

		$branch = Branch::firstOrCreate(
				[
					'code' => trim($request->input('branch-code') ?: Auth::user()->branch_code),
					'name' => trim($request->input('branch-name') ?: Auth::user()->branch()->first()->name),
					'region' => trim($request->input('region') ?: Auth::user()->branch()->first()->region)
				],
				[
					'user_id' => Auth::id()
				]
			);

		$salesDeal = SalesDealModel::create([
			'user_id' => ($request->input('dealer-id') ?: Auth::id()),
			'account_id' => $account->id,
			'branch_id' => $branch->id,
			'currency_pair_id' => $request->input('currency_id'),
			'base_currency_closing_rate_id' => $baseCurrencyClosingRateId,
			'interoffice_rate' => ($request->input('interoffice_rate') ?: 0),
			'customer_rate' => ($request->input('customer-rate') ?: 0),

			'amount' => (
                Str::of($request->input('buy-sell'))->lower()->after('bank')->trim()->exactly('sell') ? (
                    -(floatval($request->input('amount')) ?: 0)
                ) : (
                    ($request->input('amount') ?: 0)
                )
            ),

			'tod_tom_spot_forward' => Group::where('group', 'tod_tom_spot_forward')
				->where('name', $request->input('tod-tom-spot-forward'))
				->first()
				->name_id,

			'tt_bn' => Group::where('group', 'tt_bn')
				->where('name', $request->input('tt-bn'))
				->first()
				->name_id,

			'buy_sell' => Group::where('group', 'buy_sell')
				->where('name', Str::of($request->input('buy-sell'))->lower()->after('bank')->trim())
				->first()
				->name_id,

			'lhbu_remarks_code' => $request->input('lhbu-remarks-code'),
			'lhbu_remarks_kind' => $request->input('lhbu-remarks-kind'),
		]);

		Auth::user()->save();

		if ($request->route()->named('sales-special-rate-deal.store')) {
			SpecialRateDeal::create([
				'user_id' => $salesDeal->user_id,
				'sales_deal_id' => $salesDeal->id,
			]);

		} else {
            $this->sismontavar($salesDeal);
        }

        if (!$request->route()->named('sales-special-rate-deal.store') && $salesDeal->salesDealRate) {
            $salesDeal->salesDealRate()->update([
                'base_currency_rate' => $request->input('base_currency_rate'),
            ]);
        }

		if (Str::of($salesDeal->lhbuRemarksKind->name)->exactly('dengan underlying lainnya')) {
            OtherLhbuRemarksKind::create([
				'sales_deal_id' => $salesDeal->id,
				'value' => $request->input('other-lhbu-remarks-kind'),
			]);
        }

		return redirect()->back()->with('status', 'The Dealing Was Submitted!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SalesDealModel $salesDeal)
    {
        $currencyPair = CurrencyPair::whereDate('updated_at', Carbon::today()->toDateString())
            ->whereNull('counter_currency_id')
            ->where($salesDeal->buyOrSell->name.'ing_rate', '>', 0)
            ->whereHas('baseCurrency', function($query) use($salesDeal) {
                $query->where('primary_code', $salesDeal->currencyPair->baseCurrency->primary_code);
            });

        $threshold = Threshold::latest();
		$threshold = $threshold->exists() ? floatval($threshold->first()->threshold) : 0;

		return view('sales-deal.show', [
			'salesDeal' => $salesDeal,
			'currencyPair' => $currencyPair,
			'threshold' => $threshold,
		]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SalesDealModel $salesDeal)
    {
        $regions;

		try {
            $regions = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('NamaRegion as region')
                ->where('Company name', 'not like', '%'.strtoupper('(tutup)'))
                ->whereNotNull('NamaRegion')
                ->get();

        } catch (\Exception $e) {
            $regions = Branch::select('region')
                ->whereNotNull('region')
                ->get();
        }

        $regions = $regions->map( function($item) {
                if ($item instanceof Branch) {
                    $item = $item->toArray();
                } else {
                    $item = ((array) $item);
                }

                return ((object) array_map('htmlspecialchars_decode', $item));
            });

        $threshold = Threshold::latest();
		$threshold = $threshold->exists() ? floatval($threshold->first()->threshold) : 0;

        $lhbuRemarksCode = Group::where('group', 'lhbu_remarks_code')
            ->get(['name_id as id', 'name'])
            ->map( function($item) {
                $item->text = substr('0'.((string) $item->id), -2);
                $item->text .= ' ';
                $item->text .= ucfirst($item->name);

                return $item;
            });

        $lhbuRemarksKind = Group::where('group', 'lhbu_remarks_kind')
            ->get(['name_id as id', 'name'])
            ->map( function($item) {
                $item->text = substr('00'.((string) $item->id), -3);
                $item->text .= ' ';
                $item->text .= ucfirst($item->name);

                return $item;
            });

		return view('sales-deal.edit', [
			'salesDeal' => $salesDeal,
			'threshold' => $threshold,
			'regions' => $regions,
			'lhbuRemarksCode' => $lhbuRemarksCode,
			'lhbuRemarksKind' => $lhbuRemarksKind,
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalesDealModel $salesDeal)
    {
		$account = Account::firstOrCreate(
				[
					'number' => ($request->input('account-number') ?: $salesDeal->account->number),
					'cif' => ($request->input('account-cif') ?: $salesDeal->account->cif),
					'name' => trim($request->input('account-name') ?: $salesDeal->account->name)
				],
				[
					'user_id' => Auth::id(),
				]
			);

		$branch = Branch::firstOrCreate(
				[
					'code' => trim($request->input('branch-code') ?: $salesDeal->branch->code),
					'name' => trim($request->input('branch-name') ?: $salesDeal->branch->name),
					'region' => trim($request->input('region') ?: $salesDeal->branch->region)
				],
				[
					'user_id' => Auth::id()
				]
			);

		$request->validate([
			'lhbu-remarks-kind' => [
				'required',
				Rule::unique($salesDeal->getTable(), 'lhbu_remarks_kind')->where(function ($query) use($request, $salesDeal, $account, $branch) {
					$query->where([
						'id' => $salesDeal->id,
						'user_id' => $salesDeal->user_id,
						'account_id' => $account->id,
						'branch_id' => $branch->id,
						'currency_pair_id' => ($request->input('currency_id') ?: $salesDeal->currency_pair_id),
						'interoffice_rate' => ($request->input('interoffice-rate') ?: $salesDeal->interoffice_rate),
						'customer_rate' => ($request->input('customer-rate') ?: $salesDeal->customer_rate),

                        'amount' => ($request->input('amount') ?: $salesDeal->amount),

                        'tod_tom_spot_forward' => Group::where('group', 'tod_tom_spot_forward')
                            ->where('name', ($request->input('tod-tom-spot-forward') ?: $salesDeal->todOrTomOrSpotOrForward->name))
                            ->first()
                            ->name_id,

                        'tt_bn' => Group::where('group', 'tt_bn')
                            ->where('name', ($request->input('tt-bn') ?: $salesDeal->ttOrBn->name))
                            ->first()
                            ->name_id,

                        'buy_sell' => Group::where('group', 'buy_sell')
                            ->where('name', (Str::of($request->input('buy-sell'))->lower()->after('bank')->trim() ?: $salesDeal->buyOrSell->name))
                            ->first()
                            ->name_id,

						'lhbu_remarks_code' => ($request->input('lhbu-remarks-code') ?: $salesDeal->lhbu_remarks_code),
						'lhbu_remarks_kind' => ($request->input('lhbu-remarks-kind') ?: $salesDeal->lhbu_remarks_kind)
					]);
				})
			]
		]);

		$salesDeal = SalesDealModel::whereHas('modificationUpdated', function($query) {
				$query->where('confirmed', false);
			})
			->updateOrCreate(
				[
					'id' => $salesDeal->id
				],
				[
					'user_id' => $salesDeal->user_id,
					'account_id' => $account->id,
					'branch_id' => $branch->id,
					'currency_pair_id' => ($request->input('currency_id') ?: $salesDeal->currency_pair_id),
                    'base_currency_closing_rate_id' => $salesDeal->base_currency_closing_rate_id,
					'interoffice_rate' => ($request->input('interoffice-rate') ?: $salesDeal->interoffice_rate),
					'customer_rate' => ($request->input('customer-rate') ?: $salesDeal->customer_rate),

                    'amount' => ($request->input('amount') ?: $salesDeal->amount),

					'tod_tom_spot_forward' => Group::where('group', 'tod_tom_spot_forward')
                        ->where('name', ($request->input('tod-tom-spot-forward') ?: $salesDeal->todOrTomOrSpotOrForward->name))
                        ->first()
                        ->name_id,

                    'tt_bn' => Group::where('group', 'tt_bn')
                        ->where('name', ($request->input('tt-bn') ?: $salesDeal->ttOrBn->name))
                        ->first()
                        ->name_id,

                    'buy_sell' => Group::where('group', 'buy_sell')
                        ->where('name', (Str::of($request->input('buy-sell'))->lower()->after('bank')->trim() ?: $salesDeal->buyOrSell->name))
                        ->first()
                        ->name_id,

					'lhbu_remarks_code' => ($request->input('lhbu-remarks-code') ?: $salesDeal->lhbu_remarks_code),
					'lhbu_remarks_kind' => ($request->input('lhbu-remarks-kind') ?: $salesDeal->lhbu_remarks_kind),
				]
			);

		Modification::updateOrCreate(
			[
				'deal_updated_id' => $salesDeal->id,
				'interbank_sales' => Group::where('group', 'interbank_sales')
					->where('name', 'sales')
					->first()
					->name_id,				
			],
			[
				'user_id' => Auth::id(),
				'deal_created_id' => $salesDeal->wasChanged() ? (
						$salesDeal->modificationUpdated->deal_created_id
					) : (
						$request->route()->originalParameter('salesDeal')
					),
			]
		);

		if ($request->route()->parameter('salesDeal')->specialRateDeal) {
			SpecialRateDeal::firstOrCreate(
				['sales_deal_id' => $salesDeal->id],

				$request->route()->parameter('salesDeal')->specialRateDeal
				->makeHidden(['id', 'sales_deal_id'])
				->toArray()
			);
		}

		if ($request->route()->parameter('salesDeal')->salesDealFile) {
			SalesDealFile::firstOrCreate(
				['sales_deal_id' => $salesDeal->id],

				$request->route()->parameter('salesDeal')->salesDealFile
				->makeHidden(['id', 'sales_deal_id'])
				->toArray()
			);
		}

		if (Str::of($salesDeal->lhbuRemarksKind->name)->exactly('dengan underlying lainnya')) {
            OtherLhbuRemarksKind::updateOrCreate(
                [
                    'sales_deal_id' => $salesDeal->id,
                ],
                [
                    'value' => $request->input('other-lhbu-remarks-kind'),
                ]
            );

        } elseif ($salesDeal->otherLhbuRemarksKind()->exists()) {
            $salesDeal->otherLhbuRemarksKind()->delete();
        }

		if (Auth::user()->is_administrator) {
			Modification::whereHas('interbankOrSales', function($query) {
				$query->where('name', 'sales');
			})
			->where('deal_updated_id', $salesDeal->id)
			->update([
				'confirmed' => true
			]);
		}

        if ($salesDeal->wasRecentlyCreated) {
            $salesDeal->fill([
				'created_at' => $request->route()->parameter('salesDeal')->created_at
			])
            ->save();
        }

		Auth::user()->save();

		return redirect()->route(Str::before($request->route()->getName(), '.').'.index')->with('status', 'The Dealing Was Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

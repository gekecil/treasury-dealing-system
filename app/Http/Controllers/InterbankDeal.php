<?php

namespace App\Http\Controllers;

use App\InterbankDeal as InterbankDealModel;
use App\Counterparty;
use App\CurrencyPair;
use App\Currency;
use App\ClosingRate;
use App\Group;
use App\Market;
use App\Modification;
use App\SettlementDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InterbankDeal extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(InterbankDealModel::class, 'interbankDeal');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return view('interbank-deal.index');
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
            'counterparty' => 'required',
            'buy-sell' => 'required',
            'tod-tom-spot-forward' => 'required',
            'base-currency-code' => [
                'required',
                Rule::exists((new Currency)->getTable(), 'primary_code')
                ->where(function ($query) use($request) {
                    $query->where('id', (
                        ClosingRate::where('currency_id', (
                            Currency::whereNull('secondary_code')
                            ->firstOrNew(
                                ['primary_code' => $request->input('base-currency-code')],
                                ['id' => null]
                            )
                            ->id
                        ))->firstOrNew(
                            [
                                'created_at' =>  Market::whereDate('closing_at', '<', Carbon::today()->toDateString())
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
                            ],
                            [
                                'currency_id' => null
                            ]
                        )
                        ->currency_id
                    ));
                }),
            ],
        ]);

		$counterparty = Counterparty::firstOrCreate([
				'name' => trim($request->input('counterparty'))
			]);

		$interbankDeal = InterbankDealModel::create([
			'user_id' => ($request->input('dealer-id') ?: Auth::user()->id),
			'counterparty_id' => $counterparty->id,

			'currency_pair_id' => (
                CurrencyPair::where( function($query) use($request) {
                    $query->whereHas('baseCurrency', function($query) use($request) {
                        $query->where('primary_code', $request->input('base-currency-code'));
                    });

                    if ($request->filled('counter-currency-code')) {
                        $query->whereHas('counterCurrency', function($query) use($request) {
                            $query->where('primary_code', $request->input('counter-currency-code'));
                        });

                    } else {
                        $query->whereNull('counter_currency_id');
                    }
                })
				->first()
				->id
            ),

			'base_currency_closing_rate_id' => (
                ClosingRate::firstWhere([
                    'currency_id' => Currency::whereNull('secondary_code')
                        ->firstOrNew(
                            ['primary_code' => $request->input('base-currency-code')],
                            ['id' => null]
                        )
                        ->id,

                    'created_at' =>  Market::whereDate('closing_at', '<', Carbon::today()->toDateString())
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
				->id
            ),

			'interoffice_rate' => ($request->input('interoffice-rate') ?: 0),

            'amount' => (
                (strtolower(trim($request->input('buy-sell'))) === 'bank sell') ? (
                    -(floatval($request->input('amount')) ?: 0)
                ) : (
                    ($request->input('amount') ?: 0)
                )
            ),

			'tod_tom_spot_forward' => Group::where('group', 'tod_tom_spot_forward')
				->where('name', $request->input('tod-tom-spot-forward'))
				->first()
				->name_id,

			'buy_sell' => Group::where('group', 'buy_sell')
				->where('name', Str::of($request->input('buy-sell'))->lower()->after('bank')->trim())
				->first()
				->name_id,

			'basic_remarks' => $request->input('basic-remarks'),
			'additional_remarks' => $request->input('additional-remarks'),
		]);

        if ($interbankDeal->interbankDealRate) {
            $interbankDeal->interbankDealRate()->update([
                'base_currency_rate' => ($request->input('base-currency-rate') ?: 0),
            ]);
        }

		if (strtolower($interbankDeal->todOrTomOrSpotOrForward->name) !== 'tod') {
            SettlementDate::create([
				'interbank_deal_id' => $interbankDeal->id,
				'value' => $request->input('settlement-date'),
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
    public function show(InterbankDealModel $interbankDeal)
    {
		return view('interbank-deal.show', [
			'interbankDeal' => $interbankDeal
		]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InterbankDealModel $interbankDeal)
    {
		return view('interbank-deal.edit', [
			'interbankDeal' => $interbankDeal
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InterbankDealModel $interbankDeal)
    {
		$counterparty = Counterparty::firstOrCreate([
				'name' => trim($request->input('counterparty'))
			]);

		$interbankDeal->fill([
			'user_id' => ($request->input('dealer-id') ?: Auth::user()->id),
			'counterparty_id' => $counterparty->id,
            'interoffice_rate' => ($request->input('interoffice-rate') ?: $interbankDeal->interoffice_rate),
            'amount' => ($request->input('amount') ?: $interbankDeal->amount),

			'tod_tom_spot_forward' => Group::where('group', 'tod_tom_spot_forward')
				->where('name', $request->input('tod-tom-spot-forward'))
				->first()
				->name_id,

			'basic_remarks' => $request->input('basic-remarks'),
			'additional_remarks' => $request->input('additional-remarks'),
		])
		->save();

        if ($interbankDeal->interbankDealRate) {
            $interbankDeal->interbankDealRate()->update([
                'base_currency_rate' => ($request->input('base-currency-rate') ?: 0),
            ]);
        }

		if (strtolower($interbankDeal->todOrTomOrSpotOrForward->name) !== 'tod') {
            SettlementDate::updateOrCreate(
                [
                    'interbank_deal_id' => $interbankDeal->id
                ],
                [
                    'value' => $request->input('settlement-date')
                ]
			);
        }

		return redirect()->action(class_basename($this).'@index')->with('status', 'The Interbank Deal Was Updated!');
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

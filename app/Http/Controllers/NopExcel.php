<?php

namespace App\Http\Controllers;

use App\InterbankDeal;
use App\SalesDeal;
use App\NopAdjustment;
use App\CurrencyPair;
use App\Currency;
use App\SettlementDate;
use App\Market;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class NopExcel extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', InterbankDeal::class);
        $this->authorize('viewAny', SalesDeal::class);
        $this->authorize('viewAny', NopAdjustment::class);

        set_time_limit(300);

		$columns = collect([
			'No',
			'Counterparty',
			'Branch',
			'Value',
			'Currency Pairs',
			'',
			'Amount',
			'Customer Rate',
			'Interoffice Rate',
			'Customer Amount',
			'Interoffice Amount',
			'Branch PL',
			'Remarks',
			'',
		]);

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getProperties()->setCreator('Treasury Team')
					->setLastModifiedBy('Treasury Team')
					->setTitle('Office 2007 XLSX Treasury')
					->setSubject('Office 2007 XLSX Treasury');

		$alphabets = collect([]);
		foreach (range('A', 'Z') as $alphabet) {
			$alphabets->push($alphabet);
		}

		$worksheet = [];

		$currency = Currency::selectRaw('max(case when secondary_code is null then id else 0 end) as id')
            ->addSelect('primary_code as currency_code')
            ->addSelect(DB::raw("array_agg(id) as ids"))
            ->where('id', '!=', 1)
            ->groupBy('primary_code')
            ->orderBy('id')
            ->get()
            ->prepend(
				Currency::withTrashed()
                ->select('id')
                ->addSelect('primary_code as currency_code')
                ->addSelect(DB::raw('array[id] as ids'))
				->find(1)
			);

		$blotter = InterbankDeal::select(
				'id',
                'counterparty_id',
                'currency_pair_id',
                'base_currency_closing_rate_id',
                'interoffice_rate',
                'amount',
                'buy_sell',
                'tod_tom_spot_forward',
                'basic_remarks',
                'additional_remarks',
                'created_at'
			)
			->where( function($query) use($request) {
				if ($request->filled('date_from')) {
					$query->whereDate('created_at', '>=', $request->input('date_from'));
				}

                if ($request->filled('date_to')) {
					$query->whereDate('created_at', '<=', $request->input('date_to'));
				}
			})
			->get()
			->concat(
				SalesDeal::select(
					'id', 'branch_id', 'currency_pair_id', 'base_currency_closing_rate_id', 'interoffice_rate', 'customer_rate', 'amount', 'buy_sell', 'tod_tom_spot_forward', 'created_at'
				)
				->where( function($query) use($request) {
					if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->input('date_from'));
                    }

                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->input('date_to'));
                    }
				})
				->confirmed()
				->doesntHave('cancellation')
				->get()
			)
			->concat(
				NopAdjustment::select('currency_id', 'base_currency_closing_rate_id', 'amount', 'created_at')
                ->addSelect('currency_id as currency_pair_id')
				->where( function($query) use($request) {
                    if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->input('date_from'));
                    }

                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->input('date_to'));
                    }
				})
				->get()
			)
            ->sortByDesc('created_at');

		$mergeCells = collect([
			['E', 'F'],
            ['M', 'N'],
		]);

		$currencyPair = new CurrencyPair;

		$blotter->groupBy( function ($item, $key) {
			return $item->created_at->toDateString();
		})
		->each( function($items, $date) use(
            $alphabets, $spreadsheet, $worksheet, $columns, $mergeCells, $currency, $blotter, $currencyPair
        ) {
            if ($spreadsheet->getSheet(0)->getTitle() === $spreadsheet->getSheet(0)->getCodeName()) {
				$worksheet[$date] = $spreadsheet->getActiveSheet();
				
			} else {
				$worksheet[$date] = $spreadsheet->createSheet();
			}
			
			$worksheet[$date]->setTitle($items->first()->created_at->toDateString());
			
			$worksheet[$date]->getStyle(
				$alphabets->first().(1).':'.$alphabets->last().(1)
			)
			->getAlignment()
			->setHorizontal('center');
			
			$worksheet[$date]->getStyle(
				$alphabets->first().(1).':'.$alphabets->last().(1)
			)
			->getFont()
			->setBold(true);
			
			collect([
				'Currency',
				'Opening NOP',
				'Opening Rate',
				'Average Rate',
				'Revaluation Rate',
				'Current NOP',
				'TOM NOP',
				'SPOT NOP',
				'Forward NOP',
				'On Balance Sheet',
				'Off Balance Sheet',
				'USD NOP',
				'Absolute NOP',
				'Profit/Loss',
			])
			->each( function($item, $key) use($alphabets, $worksheet, $date, $columns) {
				$worksheet[$date]->getCell($alphabets->get($key).(1))->setValue($item);
				
				if (($columns->has($key - 1) && !$columns->get($key - 1) && !$item) || (!$item)) {
					$worksheet[$date]->getColumnDimension($alphabets->get($key))->setWidth(10);
					
				} else {
					$worksheet[$date]->getColumnDimension($alphabets->get($key))->setAutoSize(true);
				}
			});
			
			$worksheet[$date]->getStyle(
				$alphabets->first().($currency->count() + 3).':'.
				$alphabets->last().($currency->count() + 3)
			)
			->getAlignment()
			->setHorizontal('center');
			
			$worksheet[$date]->getStyle(
				$alphabets->first().($currency->count() + 3).':'.
				$alphabets->last().($currency->count() + 3)
			)
			->getFont()
			->setBold(true);
			
			$columns->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $columns) {
				$worksheet[$date]->getCell($alphabets->get($key).($currency->count() + 3))->setValue($item);
				
				if (($columns->has($key - 1) && !$columns->get($key - 1) && !$item) || (!$item)) {
					$worksheet[$date]->getColumnDimension($alphabets->get($key))->setWidth(10);
					
				} else {
					$worksheet[$date]->getColumnDimension($alphabets->get($key))->setAutoSize(true);
				}
			});
			
			$mergeCells->each( function($item, $key) use($worksheet, $date, $currency, $mergeCells) {
				$worksheet[$date]->mergeCells(
					$mergeCells->only([$key])->collapse()->join(($currency->count() + 3).':').(
						$currency->count() + 3
					)
				);
			});

            $openingDeal = $blotter->where('created_at', '<', $date);

			$currency->values()
			->each( function($item, $key) use($alphabets, $worksheet, $items, $date, $openingDeal, $currencyPair, $currency) {
                if ($openingDeal->isNotEmpty()) {
                    $item->amount = (
                            '=IFNA(VLOOKUP(E'.(($currency->count() + 3) + $key + 1).
                            ',\''.$openingDeal->first()->created_at->toDateString().
                            '\'!A1:\''.$openingDeal->first()->created_at->toDateString().
                            '\'!F'.($currency->count() + 1).',6,FALSE),0)'
                        );

                    $item->interoffice_rate = (
                            $item->closingRate()
                            ->firstOrNew(
                                [
                                    'created_at' => $openingDeal->first()->created_at->toDateString()
                                ],
                                [
                                    'mid_rate' => null
                                ]
                            )
                            ->mid_rate
                        );

                } elseif (
                    InterbankDeal::whereDate('created_at', $date)->get()
                    ->concat(
                        SalesDeal::whereDate('created_at', $date)
                        ->confirmed()
                        ->doesntHave('cancellation')
                        ->get()
                    )
                    ->concat(
                        NopAdjustment::whereDate('created_at', $date)
                        ->get()
                    )
                    ->isNotEmpty()
                ) {
                    $item->amount = (
                            InterbankDeal::join($currencyPair->getTable(), function($join) {
                                $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                            })
                            ->where( function($query) use($date) {
                                $query->whereDate($query->getModel()->getTable().'.created_at', '<', $date);
                            })
                            ->where( function($query) use($item) {
                                $query->whereRaw("base_currency_id = any ('".$item->ids."')")
                                ->orWhereRaw("counter_currency_id = any ('".$item->ids."')");
                            })
                            ->sum(
                                DB::raw(
                                    "case when base_currency_id = any ('".$item->ids."')".
                                    " then amount else (-(amount) * interoffice_rate) end"
                                )
                            ) + (
                                SalesDeal::join($currencyPair->getTable(), function($join) {
                                    $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                                })
                                ->where( function($query) use($date) {
                                    $query->whereDate($query->getModel()->getTable().'.created_at', '<', $date);
                                })
                                ->where( function($query) use($item) {
                                    $query->whereRaw("base_currency_id = any ('".$item->ids."')")
                                    ->orWhereRaw("counter_currency_id = any ('".$item->ids."')");
                                })
                                ->confirmed()
                                ->doesntHave('cancellation')
                                ->sum(
                                    DB::raw(
                                        "case when base_currency_id = any ('".$item->ids."')".
                                        " then amount else (-(amount) * customer_rate) end"
                                    )
                                )
                            ) + (
                                NopAdjustment::whereDate('created_at', '<', $date)
                                ->whereRaw("currency_id = any ('".$item->ids."')")
                                ->sum('amount')
                            )
                        );

                    $item->interoffice_rate = (
                            $item->closingRate()
                            ->firstOrNew(
                                [
                                    'created_at' => (
                                            InterbankDeal::whereDate('created_at', $date)->get()
                                            ->concat(
                                                SalesDeal::whereDate('created_at', $date)
                                                ->confirmed()
                                                ->doesntHave('cancellation')
                                                ->get()
                                            )
                                            ->concat(
                                                NopAdjustment::whereDate('created_at', $date)
                                                ->get()
                                            )
                                            ->first()
                                            ->baseCurrencyClosingRate
                                            ->created_at
                                            ->toDateString()
                                        )
                                ],
                                [
                                    'mid_rate' => null
                                ]
                            )
                            ->mid_rate
                        );
                }

				$values = collect([
					($key + 1),
					
					strtoupper('opening'),
					
					'',
					
					'',
					
					$item->currency_code,
					
					'IDR',
					
					$item->amount,
					
					'',
					
					$item->interoffice_rate,
					
					'',
					'=I'.($currency->count() + $key + 4).'*G'.($currency->count() + $key + 4),
					'',
					''
				]);
				
				$values->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $values) {
					$worksheet[$date]->getCell(
						$alphabets->get($key).(
							($currency->count() + 3) + ($values->get(0))
						))->setValue($item);
				});
				
			});

			$salesDeal = $items->whereInstanceOf(SalesDeal::class)
				->sortBy('currency_id')
				->flatMap(function ($values) {
					$values = collect([
							(object) (
								[
									'currency_code' => $values->currencyPair->baseCurrency->primary_code,

									'counter_currency_code' => $values->currencyPair->counterCurrency()
                                        ->firstOrNew([], ['primary_code' => null])
                                        ->primary_code,

									'branch' => $values->branch->name,
									'tod_tom_spot_forward' => $values->todOrTomOrSpotOrForward->name,
									'buy_sell' => $values->buyOrSell->name,
									'amount' => $values->amount,
									'interoffice_rate' => $values->interoffice_rate,
									'customer_rate' => $values->customer_rate,
                                    'sales_deal_rate' => $values->salesDealRate,
								]
							)
						]);

					if ($values->get(0)->counter_currency_code) {
						$values->push(
							(object) (
								[
									'currency_code' => $values->get(0)->counter_currency_code,
									'branch' => $values->get(0)->branch,
									'tod_tom_spot_forward' => $values->get(0)->tod_tom_spot_forward,
									'buy_sell' => $values->get(0)->buy_sell,
									'amount' => (-($values->get(0)->amount) * $values->get(0)->customer_rate),
									'interoffice_rate' => ($values->get(0)->sales_deal_rate->base_currency_rate / $values->get(0)->interoffice_rate),
									'customer_rate' => ($values->get(0)->sales_deal_rate->base_currency_rate / $values->get(0)->customer_rate),
								]
							)
						);
					}

					return $values->all();
				});

			$salesDeal->values()
			->each( function($item, $key) use($alphabets, $worksheet, $date, $currency) {
                if (property_exists($item, 'sales_deal_rate')) {
                    $item->customer_rate = ($item->sales_deal_rate ?: ((object) (['base_currency_rate' => $item->customer_rate])))
                        ->base_currency_rate;

                    $item->interoffice_rate = ($item->sales_deal_rate ?: ((object) (['base_currency_rate' => $item->interoffice_rate])))
                        ->base_currency_rate;
                }

				$values = collect([
					(($key + 1) + $currency->count()),
					'',
					$item->branch,
					ucwords($item->tod_tom_spot_forward),
					$item->currency_code,
					'IDR',
                    $item->amount,
                    $item->customer_rate,
                    $item->interoffice_rate,

					'=H'.(
						($key + 5) + ($currency->count() * 2)
					).
					'*G'.(
						($key + 5) + ($currency->count() * 2)
					),
					
					'=I'.(
						($key + 5) + ($currency->count() * 2)
					).
					'*G'.(
						($key + 5) + ($currency->count() * 2)
					),

					'=(I'.(
						($key + 5) + ($currency->count() * 2)
					).
					'-H'.(
						($key + 5) + ($currency->count() * 2)
					).
					')*G'.(
						($key + 5) + ($currency->count() * 2)
					),

					''
				]);

				$values->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $values) {
					$worksheet[$date]->getCell(
						$alphabets->get($key).($currency->count() + $values->get(0) + 4)
					)->setValue($item);
				});

			});

			$interbankDeal = $items->whereInstanceOf(InterbankDeal::class)
				->sortBy('currency_id')
				->flatMap(function ($values) {
					$values = collect([
							(object) (
								[
									'currency_code' => $values->currencyPair->baseCurrency->primary_code,

									'counter_currency_code' => $values->currencyPair->counterCurrency()
                                        ->firstOrNew([], ['primary_code' => null])
                                        ->primary_code,

									'counterparty' => $values->counterparty->name,
									'tod_tom_spot_forward' => $values->todOrTomOrSpotOrForward->name,
									'buy_sell' => $values->buyOrSell->name,
									'amount' => $values->amount,
									'interoffice_rate' => $values->interoffice_rate,
                                    'interbank_deal_rate' => $values->interbankDealRate,
									'basic_remarks' => $values->basic_remarks,
									'additional_remarks' => $values->additional_remarks,
								]
							)
						]);

					if ($values->get(0)->counter_currency_code) {
						$values->push(
							(object) (
								[
									'currency_code' => $values->get(0)->counter_currency_code,
									'counterparty' => $values->get(0)->counterparty,
									'tod_tom_spot_forward' => $values->get(0)->tod_tom_spot_forward,
									'buy_sell' => $values->get(0)->buy_sell,
									'basic_remarks' => $values->get(0)->basic_remarks,
									'additional_remarks' => $values->get(0)->additional_remarks,
									'amount' => (-($values->get(0)->amount) * $values->get(0)->interoffice_rate),

									'interoffice_rate' => (
                                            $values->get(0)->interbank_deal_rate->base_currency_rate / $values->get(0)->interoffice_rate
                                        ),
								]
							)
						);
					}

					return $values->all();
				});

			$interbankDeal->values()
			->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $salesDeal) {
                if (property_exists($item, 'interbank_deal_rate')) {
                    $item->interoffice_rate = ($item->interbank_deal_rate ?: ((object) (['base_currency_rate' => $item->interoffice_rate])))
                        ->base_currency_rate;
                }

				$values = collect([
					(($key + 1) + $currency->count() + $salesDeal->count()),
					$item->counterparty,
					'',
					ucwords($item->tod_tom_spot_forward),
					$item->currency_code,
					'IDR',
                    $item->amount,
                    $item->interoffice_rate,
                    $item->interoffice_rate,

					'=H'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					).
					'*G'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					),
					
					'=I'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					).
					'*G'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					),
					
					'=(I'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					).
					'-H'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					).
					')*G'.(
						($key + 6) + ($currency->count() * 2) + $salesDeal->count()
					),
					
					$item->basic_remarks,
					$item->additional_remarks,
				]);
				
				$values->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $values) {
					$worksheet[$date]->getCell(
						$alphabets->get($key).($currency->count() + $values->get(0) + 5)
					)->setValue($item);
				});
				
			});
			
			$nopAdjustment = $items->whereInstanceOf(NopAdjustment::class);
			
			$nopAdjustment->sortBy('currency_id')
			->values()
			->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $salesDeal, $interbankDeal) {
				$values = collect([
					(($key + 1) + $currency->count() + $salesDeal->count() + $interbankDeal->count()),
					strtoupper('nop adjustment'),
					'',
					'',
					$item->currency->primary_code,
					'IDR',
					$item->amount,
					$item->baseCurrencyClosingRate->mid_rate,
					$item->baseCurrencyClosingRate->mid_rate,

					'=H'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					).
					'*G'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					),

					'=I'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					).
					'*G'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					),

					'=(I'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					).
					'-H'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					).
					')*G'.(
						($key + 7) + ($currency->count() * 2) + $salesDeal->count() + $interbankDeal->count()
					),
					''
				]);

				$values->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $values) {
					$worksheet[$date]->getCell(
						$alphabets->get($key).($currency->count() + $values->get(0) + 6)
					)->setValue($item);
				});

			});

            $currency->values()
			->each( function($item, $key) use($alphabets, $worksheet, $date, $currency, $currencyPair, $salesDeal, $interbankDeal, $nopAdjustment) {
                $nextMarket = Market::whereDate('closing_at', '>', $date)
                    ->orderBy('closing_at')
                    ->take(2)
                    ->get();

                $item->row = ($key + 2);

				if ($nextMarket->count() >= 2) {
                    $item->settlements = SettlementDate::whereHas('interbankDeal', function($query) use($currencyPair, $item, $date) {
                            $query->join($currencyPair->getTable(), function($join) {
                                $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                            })
                            ->where( function($query) use($item) {
                                $query->whereRaw("base_currency_id = any ('".$item->ids."')")
                                ->orWhereRaw("counter_currency_id = any ('".$item->ids."')");
                            })
                            ->where( function($query) use($date) {
                                $query->whereDate($query->getModel()->getTable().'.created_at', '<', $date);
                            });
                        })
                        ->where('value', '>', $date)
                        ->get()
                        ->groupBy( function($item) use($nextMarket) {
                            if($nextMarket->first()->closing_at->isSameDay($item->value)) {
                                return 'TOM';

                            } elseif($nextMarket->last()->closing_at->isSameDay($item->value)) {
                                return 'spot';
                            }

                            return 'forward';
                        })
                        ->map( function($items) use($currencyPair, $item) {
                            return $items->sum( function($settlement) use($currencyPair, $item) {
                                return $settlement->interbankDeal()
                                    ->join($currencyPair->getTable(), function($join) {
                                        $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                                    })
                                    ->first(
                                        DB::raw(
                                            "case when base_currency_id = any ('".$item->ids."')".
                                            " then amount else (-(amount) * interoffice_rate) end as amount"
                                        )
                                    )
                                    ->amount;
                            });
                        });
                }

				$values = collect([
					$item->currency_code,

					'=SUMIF(E'.($currency->count() + 4).':E'.((($currency->count() * 2) ?: 1) + 3).',A'.($key + 2).
                    ',G'.($currency->count() + 4).':G'.((($currency->count() * 2) ?: 1) + 3).')',

					'=IFNA(VLOOKUP(A'.($key + 2).',E'.($currency->count() + 4).':I'.((($currency->count() * 2) ?: 1) + 3).',5,FALSE),0)',

					'=SUMIF(E'.($currency->count() + 4).':E'.(
						(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
					).
					',A'.($key + 2).',K'.($currency->count() + 4).':K'.(
						(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
					).
					')/F'.($key + 2),

					$item->closingRate()
                    ->where('created_at', $date)
                    ->firstOr( function() use($item, $currencyPair) {
                        return $item->closingRate()->getModel()->forceFill([
                            'mid_rate' => $currencyPair->whereRaw("base_currency_id = any ('".$item->ids."')")
                                ->whereNull('counter_currency_id')
                                ->whereHas('baseCurrency', function($query) {
                                    $query->whereNull('secondary_code');
                                })
                                ->get()
                                ->map( function($item, $key) {
                                    $item->mid_rate = $item->selling_rate;

                                    return $item;
                                })
                                ->pluck('mid_rate')
                                ->first()
                        ]);
                    })
                    ->mid_rate,

					'=SUMIF(E'.($currency->count() + 4).':E'.(
						(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
					).
					',A'.($key + 2).',G'.($currency->count() + 4).':G'.(
						(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
					).
					')',

					(($nextMarket->count() >= 2) ? (
                        '='.$item->settlements->get('TOM').'+'.
                        'SUMIFS(G'.($currency->count() + 4).':G'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        ',E'.($currency->count() + 4).':E'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        ',A'.($key + 2).',(D'.($currency->count() + 4).':D'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        '),"TOM")'
                    ) : (
                        null
                    )),

					(($nextMarket->count() >= 2) ? (
                        '='.$item->settlements->get('spot').'+'.
                        'SUMIFS(G'.($currency->count() + 4).':G'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        ',E'.($currency->count() + 4).':E'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        ',A'.($key + 2).',(D'.($currency->count() + 4).':D'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        '),"SPOT")'
                    ) : (
                        null
                    )),

					(($nextMarket->count() >= 2) ? (
                        '='.$item->settlements->get('forward').'+'.
                        'SUMIFS(G'.($currency->count() + 4).':G'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        ',E'.($currency->count() + 4).':E'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        ',A'.($key + 2).',(D'.($currency->count() + 4).':D'.(
                            (($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
                        ).
                        '),"FORWARD")'
                    ) : (
                        null
                    )),

					(($nextMarket->count() >= 2) ? (
                        '=F'.($key + 2).'-K'.($key + 2)
                    ) : (
                        null
                    )),

					(($nextMarket->count() >= 2) ? (
                        '=SUM(G'.($key + 2).':I'.($key + 2).')'
                    ) : (
                        null
                    )),

					'=IF(E'.($key + 2).',(F'.($key + 2).(
						($item->id === 1) ?(
							''
						) : (
							'*E'.($key + 2).
							'/VLOOKUP("'.$currency->where('id', 1)->pluck('currency_code')->first().'",A'.(2).':E'.($currency->count() + 1).',5,FALSE)'
						)
					)
					.'),"")',

					'=ABS(L'.($key + 2).')',

					'=IF(E'.($key + 2).',(E'.($key + 2).'-D'.($key + 2).')*F'.($key + 2).',"")',
				]);

				$worksheet[$date]->getCell('A'.($item->row))
                ->setValue($values->get($alphabets->search('A')));

				$worksheet[$date]->getCell('F'.($item->row))
                ->setValue($values->get($alphabets->search('F')));

                if ($worksheet[$date]->getCell('F'.($item->row))->getCalculatedValue() === 0.0) {
					$worksheet[$date]->getCell('F'.($item->row))
                    ->setValue(null);

                } else {
                    $values->each( function($value, $key) use($alphabets, $worksheet, $date, $item) {
                        if (!collect(['A', 'F'])->contains($alphabets->get($key))) {
                            $worksheet[$date]->getCell($alphabets->get($key).($item->row))
                            ->setValue($value);
                        }
                    });
                }

			});

			$worksheet[$date]->getStyle('A2:A'.
				(
					(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
				)
			)
			->getAlignment()
			->setHorizontal('center');
			
			$worksheet[$date]->getStyle('D2:F'.
				(
					(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
				)
			)
			->getAlignment()
			->setHorizontal('center');
			
			$worksheet[$date]->getStyle('B2:N'.($currency->count() + 1))->getNumberFormat()
			->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
			
			$worksheet[$date]->getStyle(
				'G'.($currency->count() + 4).':L'.
				(
					(($currency->count() * 2) ?: 1) + $salesDeal->count() + $interbankDeal->count() + $nopAdjustment->count() + 7
				)
			)
			->getNumberFormat()
			->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');

		});

        $spreadsheet->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header(
			'Content-Disposition: attachment;'.
				'filename="'.
					($blotter->last() ? $blotter->last()->created_at : Carbon::today())->format('d M Y').
						' - '.
							($blotter->first() ? $blotter->first()->created_at : Carbon::today())->format('d M Y').
								' Interbank Blotter.xlsx"'
		);

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

		return $writer->save('php://output');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\SalesDeal;
use App\ClosingRate;
use App\Threshold;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SalesBlotterExcel extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', SalesDeal::class);

        set_time_limit(300);

		$columns = collect([
			'Tanggal',
			'No',
			'Dealer',
			"Customer's Name",
			'TT/BN',
			'Value',
			'Currency Pairs',
			'',
			'Base Amount',
			'Customer Rate',
			'Interoffice Rate',
			'Spread',
			'Counter Amount',
			'USD Equivalent',
			'Branch PL',
			'Branch',
			'Time',
			'Authorized',
			'LHBU Remarks',
			'',
			'',
            'Keterangan',
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
		$salesDeal = SalesDeal::confirmed();
		$user = Auth::user();

		if ($user->is_branch_office_dealer) {
			$salesDeal->whereHas('branch', function($query) use($user) {
				$query->where('code', $user->branch_code);
			});

		} elseif ($request->filled('branch-code')) {
			$salesDeal->whereHas('branch', function($query) use($request) {
				$query->where('code', $request->input('branch-code'));
			});	
		}

		if ($request->filled('date_from')) {
			$salesDeal->whereDate('created_at', '>=', $request->input('date_from'));
		}

		if ($request->filled('date_to')) {
			$salesDeal->whereDate('created_at', '<=', $request->input('date_to'));
		}

		$salesDeal = $salesDeal->whereYear(
                'created_at',
                collect($salesDeal->getBindings())->filter( function($item) {
                    return strtotime($item);
                })
                ->whenEmpty( function() {
                    return Carbon::today()->toDateString();
                })
                ->map( function($item) {
                    return Carbon::parse($item)->year;
                })
                ->first()
            )
            ->oldest()
            ->get();

		$unConfirmed = SalesDeal::doesntHave('cancellation')
            ->where('created_at', '>', $salesDeal->first()->created_at->toDateTimeString())
            ->where('created_at', '<', $salesDeal->last()->created_at->toDateTimeString())
            ->where( function($query) {
                $query->whereHas('specialRateDeal', function($query) {
                    $query->where('confirmed', false);
                })
                ->orWhereHas('modificationUpdated', function($query) {
                    $query->where('confirmed', false);
                })
                ->orWhereHas('salesDealFile', function($query) {
                    $query->where('confirmed', false);
                });
            })
            ->pluck('created_at');

		$threshold = Threshold::latest();
		$threshold = $threshold->exists() ? floatval($threshold->first()->threshold) : 0;

		$year = $salesDeal->whenEmpty( function() {
                return collect([new SalesDeal(['created_at' => Carbon::today()])]);
            })
            ->first()
            ->created_at
            ->year;

		$mergeCells = collect([
			$year => collect([
				'G2:H2', 'S2:U2'
			]),
			'cancellation' => collect([
				'D2:E2'
			]),
			'today' => collect([
				'G2:H2'
			]),
			'dayOfYear' => collect([
				'F2:G2', 'R2:T2'
			]),
		]);

		$worksheet[$year] = $spreadsheet->getActiveSheet();
		$worksheet[$year]->setTitle((string) $year);
		$worksheet[$year]->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getAlignment()->setHorizontal('center');
		$worksheet[$year]->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getFont()->setBold(true);

		$columns->each( function($column, $key) use($alphabets, $mergeCells, $worksheet, $year) {
            $alphabet = $alphabets->get($key);

			$worksheet[$year]->getCell($alphabet.'2')->setValue($column);
            $worksheet[$year]->getColumnDimension($alphabet)->setAutoSize(true);
            $worksheet[$year]->calculateColumnWidths();

            if (!$worksheet[$year]->getCell($alphabet.'2')->getValue()) {
                while (!$worksheet[$year]->getCell($alphabets->get($key).'2')->getValue()) {
                    $key--;
                }

                if ($alphabets->get($key + 1) === $alphabet) {
                    $worksheet[$year]->getColumnDimension($alphabet)
                        ->setWidth($worksheet[$year]->getColumnDimension($alphabets->get($key))->getWidth() / 2);

                    $worksheet[$year]->getColumnDimension($alphabets->get($key))
                        ->setWidth($worksheet[$year]->getColumnDimension($alphabet)->getWidth());

                } else {
                    $worksheet[$year]->getColumnDimension($alphabet)
                        ->setWidth($worksheet[$year]->getColumnDimension($alphabets->get($key))->getWidth());
                }

                $worksheet[$year]->getColumnDimension($alphabet)->setAutoSize(false);
                $worksheet[$year]->getColumnDimension($alphabets->get($key))->setAutoSize(false);
            }
		});

		$mergeCells->get($year)->each( function($value, $key) use($worksheet, $year) {
			$worksheet[$year]->mergeCells($value);
		});

		$cancellation = $salesDeal->whereNotNull('cancellation')->values();
		$countCancel = $cancellation->count();

		$worksheet['cancellation'] = $spreadsheet->createSheet();
		$worksheet['cancellation']->setTitle(ucfirst('cancellation'));
		$worksheet['cancellation']->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getAlignment()->setHorizontal('center');
		$worksheet['cancellation']->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getFont()->setBold(true);

		$columns->only([0, 3, 4, 6, 7, 8, 15])->values()
		->push('Alasan')->each( function($column, $key) use($alphabets, $worksheet, $mergeCells) {
            $alphabet = $alphabets->get($key);

			$worksheet['cancellation']->getCell($alphabet.'2')->setValue($column);
            $worksheet['cancellation']->getColumnDimension($alphabet)->setAutoSize(true);
            $worksheet['cancellation']->calculateColumnWidths();

            if (!$worksheet['cancellation']->getCell($alphabet.'2')->getValue()) {
                while (!$worksheet['cancellation']->getCell($alphabets->get($key).'2')->getValue()) {
                    $key--;
                }

                if ($alphabets->get($key + 1) === $alphabet) {
                    $worksheet['cancellation']->getColumnDimension($alphabet)
                        ->setWidth($worksheet['cancellation']->getColumnDimension($alphabets->get($key))->getWidth() / 2);

                    $worksheet['cancellation']->getColumnDimension($alphabets->get($key))
                        ->setWidth($worksheet['cancellation']->getColumnDimension($alphabet)->getWidth());

                } else {
                    $worksheet['cancellation']->getColumnDimension($alphabet)
                        ->setWidth($worksheet['cancellation']->getColumnDimension($alphabets->get($key))->getWidth());
                }

                $worksheet['cancellation']->getColumnDimension($alphabet)->setAutoSize(false);
                $worksheet['cancellation']->getColumnDimension($alphabets->get($key))->setAutoSize(false);
            }
		});

		$mergeCells->get('cancellation')->each( function($value, $key) use($worksheet) {
			$worksheet['cancellation']->mergeCells($value);
		});

		$worksheet['cancellation']->getStyle('A3:A'.($countCancel + 3))->getAlignment()->setHorizontal('center');
		$worksheet['cancellation']->getStyle('B3:B'.($countCancel + 3))->getAlignment()->setHorizontal('left');
		$worksheet['cancellation']->getStyle('C3:E'.($countCancel + 3))->getAlignment()->setHorizontal('center');
		$worksheet['cancellation']->getStyle('F3:F'.($countCancel + 3))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
		$worksheet['cancellation']->getStyle('G3:H'.($countCancel + 3))->getAlignment()->setHorizontal('left');

		$cancellation->each( function($value, $row) use($alphabets, $worksheet) {
			$values = collect([
				$value->created_at->format('d-M-y'),
				$value->account->name,
				$value->ttOrBn->name,
				$value->currencyPair->baseCurrency->primary_code,
				$value->currencyPair->counterCurrency()->firstOrNew([], ['primary_code' => 'IDR'])->primary_code,
				$value->amount,
				$value->branch->name,
				$value->cancellation->note
			]);

			$values->each( function($value, $key) use($alphabets, $worksheet, $row) {
				$worksheet['cancellation']->getCell($alphabets->get($key).($row + 3))->setValue($value);
			});
		});

		$today = $salesDeal->filter( function($item, $key) {
			return $item->created_at->isToday();
		});

		if ($today->isEmpty()) {
			$today = SalesDeal::confirmed()->doesntHave('cancellation')->whereDate('created_at', Carbon::today()->toDateString());

			if ($user->is_branch_office_dealer) {
				$today->whereHas('branch', function($query) use($user) {
					$query->where('code', $user->branch_code);
				});

			} elseif ($request->filled('branch-code')) {
				$today->whereHas('branch', function($query) use($request) {
					$query->where([
						'code' => $request->input('branch-code')
					]);
				});	
			}

			$today = $today->oldest()->get();
		}

		$countToday = $today->count();

		$worksheet['today'] = $spreadsheet->createSheet();
		$worksheet['today']->setTitle(ucwords('reporting daily'));
		$worksheet['today']->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getFont()->setBold(true);

		$columns->only(2)->prepend('No')->push('Branch')->concat($columns->only([3, 4, 5, 6, 7, 8, 9, 10, 11, 14, 17]))->values()
		->each( function($column, $key) use($alphabets, $worksheet, $mergeCells) {
            $alphabet = $alphabets->get($key);

			$worksheet['today']->getCell($alphabet.'2')->setValue($column);
            $worksheet['today']->getColumnDimension($alphabet)->setAutoSize(true);
            $worksheet['today']->calculateColumnWidths();

            if (!$worksheet['today']->getCell($alphabet.'2')->getValue()) {
                while (!$worksheet['today']->getCell($alphabets->get($key).'2')->getValue()) {
                    $key--;
                }

                if ($alphabets->get($key + 1) === $alphabet) {
                    $worksheet['today']->getColumnDimension($alphabet)
                        ->setWidth($worksheet['today']->getColumnDimension($alphabets->get($key))->getWidth() / 2);

                    $worksheet['today']->getColumnDimension($alphabets->get($key))
                        ->setWidth($worksheet['today']->getColumnDimension($alphabet)->getWidth());

                } else {
                    $worksheet['today']->getColumnDimension($alphabet)
                        ->setWidth($worksheet['today']->getColumnDimension($alphabets->get($key))->getWidth());
                }

                $worksheet['today']->getColumnDimension($alphabet)->setAutoSize(false);
                $worksheet['today']->getColumnDimension($alphabets->get($key))->setAutoSize(false);
            }
		});

		$mergeCells->get('today')->each( function($value, $key) use($worksheet) {
			$worksheet['today']->mergeCells($value);
		});

		collect([
			'No',
			'Branch',
			'USD Amount',
			'IDR Profit'
		])->each( function($column, $key) use($alphabets, $worksheet, $countToday) {
			$worksheet['today']->getCell($alphabets->get($key).($countToday + 6))->setValue($column);
		});

		collect([
			'',
			'Volume(in USD)',
			'Treasury Sales PL',
		])->each( function($column, $key) use($alphabets, $worksheet, $countToday) {
			$worksheet['today']->getCell($alphabets->get($key + $alphabets->search('K')).($countToday + 6))
				->setValue($column);
		});

		$worksheet['today']->getStyle('A3:A'.($countToday + 3))->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle('E3:H'.($countToday + 3))->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle('I3:I'.($countToday + 3))->getNumberFormat()
			->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
		$worksheet['today']->getStyle('J3:M'.($countToday + 3))->getNumberFormat()->setFormatCode('#,##0.00');
		$worksheet['today']->getStyle('N3:N'.($countToday + 3))->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle('A'.($countToday + 6).':D'.($countToday + 6))->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle('A'.($countToday + 6).':D'.($countToday + 6))->getFont()->setBold(true);

		$worksheet['today']->getStyle('K'.($countToday + 6).':M'.($countToday + 6))->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle('K'.($countToday + 6).':M'.($countToday + 6))->getFont()->setBold(true);
		$worksheet['today']->getStyle('K'.($countToday + 7).':K'.($countToday + 9))->getAlignment()->setHorizontal('left');
		$worksheet['today']->getStyle('L'.($countToday + 7).':M'.($countToday + 9))->getNumberFormat()->setFormatCode('#,##0.00');

        $today = $today->sortBy('branch.name')->values();
		$cancellations = $today->whereNotNull('cancellation')->keys();

		$today->each( function($value, $row) use($alphabets, $spreadsheet, $worksheet, $cancellations) {
			$values = collect([
				($row + 1),
				$value->user->full_name,
				$value->branch->name,
				$value->account->name,
				$value->ttOrBn->name,
				ucwords($value->todOrTomOrSpotOrForward->name),
				$value->currencyPair->baseCurrency->primary_code,
				$value->currencyPair->counterCurrency()->firstOrNew([], ['primary_code' => 'IDR'])->primary_code,
				$value->amount,
				$value->customer_rate,
				$value->interoffice_rate,
				'=ABS(J'.($row + 1 + 2).'-K'.($row + 1 + 2).')',

				'=IF('.($cancellations->contains($row) ? 'FALSE' : 'TRUE').', ABS((J'.($row + 1 + 2).'-K'.($row + 1 + 2).')*I'.
                ($row + 1 + 2).'*IF(H'.($row + 1 + 2).'="IDR", 1, '.(
                    $value->currencyPair->counter_currency_id ? ($value->salesDealRate->counterCurrencyClosingRate->mid_rate) : '""'
                ).')), "")',

				$value->cancellation ? 'Cancel' : '=IF(ABS(('.$value->baseCurrencyClosingRate->mid_rate.') * I'.($row + 1 + 2).' / '.
				$value->baseCurrencyClosingRate->world_currency_closing_mid_rate.')<='.($value->user->sales_limit ?: 0).',"OK","Overlimit")'
			]);

			$values->each( function($item, $key) use($alphabets, $worksheet, $row) {
				$worksheet['today']->getCell($alphabets->get($key).($row + 1 + 2))->setValue($item);
			});

			if ($value->currencyPair->counter_currency_id) {
				$worksheet['today']->getStyle('J'.($row + 1 + 2).':K'.($row + 1 + 2))
					->getNumberFormat()->setFormatCode('#,##0.0000');
			}
		});

		$today = $today->groupBy('branch_id')->values();
		$countBranch = $today->count();

		$worksheet['today']->getStyle('A'.($countToday + 7).':A'.($countToday + $countBranch + 7))->getAlignment()->setHorizontal('center');
		$worksheet['today']->getStyle('C'.($countToday + 7).':D'.($countToday + $countBranch + 7))->getNumberFormat()
			->setFormatCode('#,##0.00');

		$todaySorted = $today->sortByDesc( function($item, $key) use($worksheet, $today) {
			return (
				collect(
					range(
                        ($today->skip(0)->take($key)->flatten(1)->count() + 3), (
                            (($today->skip(0)->take($key)->flatten(1)->count() + 3) - 1) + $item->count()
                        )
                    )
				)
				->sum( function($item) use($worksheet) {
					if (!$worksheet['today']->getCell('M'.$item)->getFormattedValue()) {
						return 0;
					}

					return $worksheet['today']->getCell('M'.$item)->getCalculatedValue();
				})
			);
		});

		$todaySorted->each( function($item, $key) use($alphabets, $worksheet, $today, $todaySorted, $countToday) {
			$values = collect([
				($todaySorted->values()->search($item) + 1),
				$item->first()->branch->name,
				$item->whereNull('cancellation')->sum('usd_equivalent'),
				'=SUM(M'.($today->skip(0)->take($key)->flatten(1)->count() + 3).':M'.((($today->skip(0)->take($key)->flatten(1)->count() + 3) - 1) + $item->count()).')'
			]);

			$values->each( function($value, $key) use($alphabets, $worksheet, $today, $todaySorted, $countToday, $item) {
				$worksheet['today']->getCell($alphabets->get($key).($countToday + 7 + $todaySorted->values()->search($item)))
                    ->setValue($value);
			});
		});

		if ($countToday > 0) {
			$worksheet['today']->getStyle('B'.($countToday + $countBranch + 7))->getAlignment()->setHorizontal('right');

			collect([
				'',
				'Total:',
				'=SUM(C'.($countToday + 7).':C'.($countToday + ($countBranch - 1) + 7).')',
				'=SUM(D'.($countToday + 7).':D'.($countToday + ($countBranch - 1) + 7).')'
			])->each( function($value, $key) use($alphabets, $worksheet, $countToday, $countBranch) {
				$worksheet['today']->getCell($alphabets->get($key).($countToday + $countBranch + 7))->setValue($value);
			});
		}

		$values = collect([
			collect([
				'Daily',
				'=C'.($countToday + $countBranch + 7),
				'=D'.($countToday + $countBranch + 7),
			]),
			collect([
				'Monthly',
				Carbon::now()->startOfMonth()
			]),
			collect([
				'Yearly',
				Carbon::now()->startOfYear()
			])
		]);

		$values = $values->except([0])->map( function($value, $key) use($user, $request) {
			$salesDeal = SalesDeal::select(
                    'id', 'account_id', 'currency_pair_id', 'base_currency_closing_rate_id', 'customer_rate', 'interoffice_rate', 'amount', 'created_at'
				)
				->confirmed()
				->doesntHave('cancellation')
				->where('created_at', '>', $value->last());

			if ($user->is_branch_office_dealer) {
				$salesDeal->whereHas('branch', function($query) use($user) {
					$query->where('code', $user->branch_code);
				});

			} elseif ($request->filled('branch-code')) {
				$salesDeal->whereHas('branch', function($query) use($request) {
					$query->where([
						'code' => $request->input('branch-code')
					]);
				});	
			}

			$salesDeal = $salesDeal->get();

			return $value->except($value->keys()->last())->values()->push($salesDeal);
		})
		->prepend($values->get(0));

		$values->except([0])
		->each( function($value, $row) use($alphabets, $worksheet, $countToday, $threshold) {
			$value->except($value->keys()->last())->values()->push($value->last()->sum('usd_equivalent'))->push($value->last()->sum('branch_pl'))
				->each( function($item, $key) use($alphabets, $worksheet, $countToday, $row) {
				$worksheet['today']->getCell($alphabets->get($key + $alphabets->search('K')).($countToday + 7 + $row))
					->setValue($item);
			});
		});

		$values->first()->values()->each( function($item, $key) use($alphabets, $worksheet, $countToday) {
			$worksheet['today']->getCell($alphabets->get($key + $alphabets->search('K')).($countToday + 7))
				->setValue($item);
		});

		$yearly = $values->last()->last();
		$values = $salesDeal->values();

		$values = $values->map( function($item, $key) use($values) {
			$item->month = $item->created_at->month;
			$item->dayOfYear = $item->created_at->dayOfYear;
			$item->row = $key + 1 - $values->forPage(1, $key + 1)
				->whereNotNull('cancellation')
				->count();

			return $item;
		})->groupBy('dayOfYear');

		$countYear = $salesDeal->count() + count($worksheet) + $values->count();

		$worksheet[$year]->getStyle('A3:A'.($countYear + 3))->getAlignment()->setHorizontal('center');
		$worksheet[$year]->getStyle('B3:D'.($countYear + 3))->getAlignment()->setHorizontal('left');
		$worksheet[$year]->getStyle('E3:H'.($countYear + 3))->getAlignment()->setHorizontal('center');
		$worksheet[$year]->getStyle('I3:O'.($countYear + 3))->getAlignment()->setHorizontal('right');
		$worksheet[$year]->getStyle('I3:I'.($countYear + 3))->getNumberFormat()
            ->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
		$worksheet[$year]->getStyle('J3:O'.($countYear + 3))->getNumberFormat()->setFormatCode('#,##0.00');
		$worksheet[$year]->getStyle('M3:M'.($countYear + 3))->getNumberFormat()
            ->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
		$worksheet[$year]->getStyle('P3:P'.($countYear + 3))->getAlignment()->setHorizontal('left');
		$worksheet[$year]->getStyle('Q3:T'.($countYear + 3))->getAlignment()->setHorizontal('center');

		$values->values()
		->reverse()
		->each( function($value, $key)
			use(
				$alphabets, $spreadsheet, $worksheet, $columns, $mergeCells, $threshold, $countToday, $user, $request, $yearly, $unConfirmed
			) {
			$count = $value->count();
			$closingRate = ClosingRate::where('created_at', $value->first()->baseCurrencyClosingRate->created_at->toDateString())
				->orderBy('currency_id')
				->get();

			$countCurrency = $closingRate->count();
			$day = $value->first()->dayOfYear;
			$cancellations = $value->whereNotNull('cancellation')->keys();

			$worksheet[$day] = $spreadsheet->createSheet();
			$worksheet[$day]->setTitle($value->first()->created_at->format('j M'));

			$worksheet[$day]->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getAlignment()->setHorizontal('center');
			$worksheet[$day]->getStyle($alphabets->first().'2:'.$alphabets->last().'2')->getFont()->setBold(true);

			$columns->except([0])->values()
			->each( function($column, $key) use($alphabets, $worksheet, $mergeCells, $day) {
                $alphabet = $alphabets->get($key);

                $worksheet[$day]->getCell($alphabet.'2')->setValue($column);
                $worksheet[$day]->getColumnDimension($alphabet)->setAutoSize(true);
                $worksheet[$day]->calculateColumnWidths();

                if (!$worksheet[$day]->getCell($alphabet.'2')->getValue()) {
                    while (!$worksheet[$day]->getCell($alphabets->get($key).'2')->getValue()) {
                        $key--;
                    }

                    if ($alphabets->get($key + 1) === $alphabet) {
                        $worksheet[$day]->getColumnDimension($alphabet)
                            ->setWidth($worksheet[$day]->getColumnDimension($alphabets->get($key))->getWidth() / 2);

                        $worksheet[$day]->getColumnDimension($alphabets->get($key))
                            ->setWidth($worksheet[$day]->getColumnDimension($alphabet)->getWidth());

                    } else {
                        $worksheet[$day]->getColumnDimension($alphabet)
                            ->setWidth($worksheet[$day]->getColumnDimension($alphabets->get($key))->getWidth());
                    }

                    $worksheet[$day]->getColumnDimension($alphabet)->setAutoSize(false);
                    $worksheet[$day]->getColumnDimension($alphabets->get($key))->setAutoSize(false);
                }
			});

			$mergeCells->get('dayOfYear')->each( function($value, $key) use($worksheet, $day) {
				$worksheet[$day]->mergeCells($value);
			});

			$worksheet[$day]->getCell('D'.($count + 5))->setValue('Closing Rate');
			$worksheet[$day]->mergeCells('D'.($count + 5).':H'.($count + 5));
			$worksheet[$day]->getStyle('D'.($count + 5).':H'.($count + 6))->getAlignment()->setHorizontal('center');
			$worksheet[$day]->getStyle('D'.($count + 5).':H'.($count + 6))->getFont()->setBold(true);

			collect([
				'Currency',
				'Bid',
				'Ask',
				'Mid',
				'Threshold',
				''
			])->each( function($column, $key) use($alphabets, $worksheet, $day, $count) {
				$worksheet[$day]->getCell($alphabets->get($key + $alphabets->search('D')).($count + 6))->setValue($column);
			});

			collect([
				'',
				'Volume(in USD)',
				'Treasury Sales PL',
			])->each( function($column, $key) use($alphabets, $worksheet, $count, $day) {
				$worksheet[$day]->getCell($alphabets->get($key + $alphabets->search('L')).($count + 6))->setValue($column);
			});

			$worksheet[$day]->getStyle('L'.($count + 6).':N'.($count + 6))->getAlignment()->setHorizontal('center');
			$worksheet[$day]->getStyle('L'.($count + 6).':N'.($count + 6))->getFont()->setBold(true);
			$worksheet[$day]->getStyle('L'.($count + 7).':L'.($count + 9))->getAlignment()->setHorizontal('left');
			$worksheet[$day]->getStyle('M'.($count + 7).':N'.($count + 9))->getNumberFormat()->setFormatCode('#,##0.00');
			$worksheet[$day]->getStyle('D'.($count + 7).':D'.($count + $countCurrency + 7))->getAlignment()->setHorizontal('center');
			$worksheet[$day]->getStyle('E'.($count + 7).':H'.($count + $countCurrency + 7))->getNumberFormat()->setFormatCode('#,##0.00');

			$closingRate->values()
			->each( function($value, $row) use($alphabets, $worksheet, $day, $count, $threshold) {
				$values = collect([
					$value->currency->primary_code,
					$value->buying_rate,
					$value->selling_rate,
					($value->buying_rate + $value->selling_rate) / 2,
					$row === 0 ? $threshold : '=H'.($count + 7).'*(G'.($count + 7).'/G'.($count + 7 + $row).')',
					''
				]);

				$values->each( function($value, $key) use($alphabets, $worksheet, $day, $count, $row) {
					$worksheet[$day]->getCell($alphabets->get($key + $alphabets->search('D')).($count + 7 + $row))->setValue($value);
				});
			});

			$volume = collect([
				collect([
					'Daily',
					'=SUM(M3:M'.($count + 3).')',
					'=SUM(N3:N'.($count + 3).')'
				]),
				collect([
					'Monthly'
				]),
				collect([
					'Yearly'
				])
			]);

			$volume->first()->each( function($item, $key) use($alphabets, $worksheet, $count, $day) {
				$worksheet[$day]->getCell($alphabets->get($key + $alphabets->search('L')).($count + 7))->setValue($item);
			});

			$volume->get(1)
				->each( function($item, $row) use($alphabets, $worksheet, $value, $user, $request, $threshold, $count, $day) {
				$salesDeal = SalesDeal::select(
                        'id', 'account_id', 'currency_pair_id', 'base_currency_closing_rate_id', 'customer_rate', 'interoffice_rate', 'amount', 'created_at'
					)
					->confirmed()
					->doesntHave('cancellation')
					->whereBetween('created_at', [
						$value->first()->created_at->startOfMonth(), $value->first()->created_at->endOfDay()
					]);

				if ($user->is_branch_office_dealer) {
					$salesDeal->whereHas('branch', function($query) use($user) {
						$query->where('code', $user->branch_code);
					});

				} elseif ($request->filled('branch-code')) {
					$salesDeal->whereHas('branch', function($query) use($request) {
						$query->where([
							'code' => $request->input('branch-code')
						]);
					});	
				}

				$salesDeal = $salesDeal->get();

				collect([$item, $salesDeal->sum('usd_equivalent'), $salesDeal->sum('branch_pl')])
					->each( function($item, $key) use($alphabets, $worksheet, $count, $day) {
					$worksheet[$day]->getCell($alphabets->get($key + $alphabets->search('L')).($count + 7 + 1))->setValue($item);
				});
			});

			$volume->last()
				->each( function($item, $row) use($alphabets, $worksheet, $yearly, $value, $threshold, $count, $day) {
				$salesDeal =	$yearly->whereBetween('created_at', [
					$value->first()->created_at->startOfYear(), $value->first()->created_at->endOfDay()
				]);

				collect([$item, $salesDeal->sum('usd_equivalent'), $salesDeal->sum('branch_pl')])
					->each( function($item, $key) use($alphabets, $worksheet, $count, $day) {
					$worksheet[$day]->getCell($alphabets->get($key + $alphabets->search('L')).($count + 7 + 2))->setValue($item);
				});
			});

			$worksheet[$day]->getStyle('A3:A'.($count + 3))->getAlignment()->setHorizontal('center');
			$worksheet[$day]->getStyle('D3:G'.($count + 3))->getAlignment()->setHorizontal('center');
			$worksheet[$day]->getStyle('H3:N'.($count + 3))->getAlignment()->setHorizontal('right');
			$worksheet[$day]->getStyle('H3:H'.($count + 3))->getNumberFormat()
                ->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
			$worksheet[$day]->getStyle('I3:N'.($count + 3))->getNumberFormat()->setFormatCode('#,##0.00');
			$worksheet[$day]->getStyle('L3:L'.($count + 3))->getNumberFormat()
                ->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"_);_(@_)');
			$worksheet[$day]->getStyle('P3:S'.($count + 3))->getAlignment()->setHorizontal('center');

			$value->each( function($value, $row) use($alphabets, $spreadsheet, $worksheet, $count, $countCurrency, $cancellations, $key, $unConfirmed) {
                $values = collect([
					($row + 1),
					$value->user->full_name,
					$value->account->name,
					$value->ttOrBn->name,
					ucwords($value->todOrTomOrSpotOrForward->name),
                    $value->currencyPair->baseCurrency->primary_code,
                    $value->currencyPair->counterCurrency()->firstOrNew([], ['primary_code' => 'IDR'])->primary_code,
					$value->amount,
					$value->customer_rate,
					$value->interoffice_rate,
					'=ABS(I'.($row + 3).'-J'.($row + 3).')',
					'=H'.($row + 3).'*I'.($row + 3),

					'=IF('.($cancellations->contains($row) ? 'FALSE' : 'TRUE').',ABS((VLOOKUP(F'.($row + 3).', D'.($count + 7).':H'.(
                        $count + $countCurrency + 6
                    ).', 4, FALSE))*H'.($row + 3).'/G'.($count + 7).'),"")',
					
					'=IF('.($cancellations->contains($row) ? 'FALSE' : 'TRUE').',ABS((I'.($row + 3).'-J'.($row + 3).')*H'.($row + 3).
                    '*IF(G'.($row + 3).'="IDR", 1, (VLOOKUP(G'.($row + 3).', D'.($count + 7).':H'.(
                        $count + $countCurrency + 6
                    ).', 4, FALSE)))),"")',

					$value->branch->name,
					$value->created_at->format('H:i:s'),

					$value->cancellation ? (
                        ucfirst('cancel')
                    ) : (
                        '=IF(ABS(M'.($row + 3).')<='.($value->user->sales_limit ?: 0).',"'.strtoupper('ok').'","'.ucfirst('overlimit').'")'
                    ),

					substr('0'.((string) $value->lhbu_remarks_code), -2),
					substr('00'.((string) $value->lhbu_remarks_kind), -3),
                    $value->otherLhbuRemarksKind()->firstOrNew([], ['value' => null])->value,
                    $value->currencyPair->baseCurrency->secondary_code,
				]);

				$values->each( function($item, $key) use($alphabets, $worksheet, $value, $row) {
					$worksheet[$value->created_at->dayOfYear]->getCell($alphabets->get($key).($row + 3))->setValue($item);
				});

				if ($value->currencyPair->counter_currency_id) {
					$worksheet[$value->created_at->dayOfYear]->getStyle('I'.($row + 3).':J'.($row + 3))
						->getNumberFormat()->setFormatCode('#,##0.0000');
				}

				if (!$value->cancellation) {
					$values = collect([
						$value->created_at->format('d-M-y'),

						($value->specialRateDeal ? 'SR' : 'FX').$value->created_at->format('dmY').'-'.substr(
                            '00'.(string) ($row + 1 + (
                                $unConfirmed->where('dayOfYear', $value->created_at->dayOfYear)
                                ->where('timestamp', '<', $value->created_at->timestamp)
                                ->count()
                            )),
                            -3
                        ),

						"='".$value->created_at->format('j M')."'!B".($row + 3),
						"='".$value->created_at->format('j M')."'!C".($row + 3),
						"='".$value->created_at->format('j M')."'!D".($row + 3),
						"='".$value->created_at->format('j M')."'!E".($row + 3),
						"='".$value->created_at->format('j M')."'!F".($row + 3),
						"='".$value->created_at->format('j M')."'!G".($row + 3),
						"='".$value->created_at->format('j M')."'!H".($row + 3),
						"='".$value->created_at->format('j M')."'!I".($row + 3),
						"='".$value->created_at->format('j M')."'!J".($row + 3),
						"='".$value->created_at->format('j M')."'!K".($row + 3),
						"='".$value->created_at->format('j M')."'!L".($row + 3),
						"='".$value->created_at->format('j M')."'!M".($row + 3),
						"='".$value->created_at->format('j M')."'!N".($row + 3),
						"='".$value->created_at->format('j M')."'!O".($row + 3),
						"='".$value->created_at->format('j M')."'!P".($row + 3),
						"='".$value->created_at->format('j M')."'!Q".($row + 3),
						"='".$value->created_at->format('j M')."'!R".($row + 3),
						"='".$value->created_at->format('j M')."'!S".($row + 3),
						"='".$value->created_at->format('j M')."'!T".($row + 3),
					]);

					$values->each( function($item, $column) use($alphabets, $worksheet, $value, $key) {
						$worksheet[$value->created_at->year]->getCell($alphabets->get($column).($value->row + $key + 2))
							->setValue($item);
					});

					if ($value->currencyPair->counter_currency_id) {
						$worksheet[$value->created_at->year]->getStyle('J'.($value->row + $key + 2).':K'.($value->row + $key + 2))
							->getNumberFormat()->setFormatCode('#,##0.0000');
					}
				}

			});
		});

		$spreadsheet->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header(
			'Content-Disposition: attachment;'.
				'filename="'.
					($salesDeal->first() ? $salesDeal->first()->created_at : Carbon::today())->format('d M').
						' - '.
							($salesDeal->last() ? $salesDeal->last()->created_at : Carbon::today())->format('d M').
								' Sales Blotter '.$year.'.xlsx"'
		);

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

		return $writer->save('php://output');
    }
}

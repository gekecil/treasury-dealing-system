<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\ClosingRate as ClosingRateModel;
use App\Currency;
use App\Threshold;
use App\Market;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClosingRate extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(ClosingRateModel::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$currency = Currency::where('id', '!=', 1)
            ->orderBy('id')
			->get()
            ->prepend(
				Currency::withTrashed()
				->find(1)
			)
            ->filter()
            ->unique('primary_code')
            ->values();

		$market = new Market;

        if ($this->request->filled('next_market_at')) {
            $market = $market->whereDate('closing_at', $this->request->input('next_market_at'));
        }

        $market = $market->latest('closing_at')
            ->firstOr( function() {
                return new Market(['closing_at' => Carbon::today()->toDateString()]);
            });

		$closingRate = ClosingRateModel::whereIn('currency_id', $currency->pluck('id'));

		if ($market->closing_at->isFuture() || $this->request->user()->can('create', Market::class)) {
			$closingRate->where('created_at', '<', $market->closing_at->toDateString());
		}

		$closingRate = $closingRate->take($currency->count() * 2)
			->latest('created_at')
			->get();

		$threshold = Threshold::latest()->firstOrNew(
				[],
				[
					'threshold' => null
				]
			);

		$currency = $currency->map( function($item, $key) use($closingRate, $threshold, $market) {
			if ($closingRate->where('currency_id', $item->id)->isNotEmpty()) {
				$item->buying_rate = $closingRate->firstWhere('currency_id', $item->id)->buying_rate;
				$item->selling_rate = $closingRate->firstWhere('currency_id', $item->id)->selling_rate;
				$item->mid_rate = (string) ((floatval($item->buying_rate) + floatval($item->selling_rate)) / 2);
				$item->created_at = $closingRate->firstWhere('currency_id', $item->id)->created_at;

				if ($item->id === 1) {
					$item->threshold = $threshold->threshold;
				} elseif ($threshold->threshold) {
					$item->threshold = (string) ($threshold->threshold * (
						(
							(
								floatval($closingRate->firstWhere('currency_id', 1)->buying_rate) +
								floatval($closingRate->firstWhere('currency_id', 1)->selling_rate)
							) /
							2
						) /
						(
							(
								floatval($closingRate->firstWhere('currency_id', $item->id)->buying_rate) +
								floatval($closingRate->firstWhere('currency_id', $item->id)->selling_rate)
							) /
							2
						)
					));
				}
			}

			if (
				$closingRate->where('currency_id', $item->id)->isNotEmpty() && (
					(
						$market->closing_at->isFuture() && (
							$closingRate->firstWhere('currency_id', $item->id)->created_at->isSameDay(
                                Market::whereDate('closing_at', '<', $market->closing_at->toDateString())
                                ->latest('closing_at')
                                ->firstOr( function() use($market) {
                                    while ($market->closing_at->isWeekend()) {
                                        $market->closing_at = $market->closing_at->subDay();
                                    }

                                    return new Market(['closing_at' => $market->closing_at->toDateString()]);
                                })
                                ->closing_at
                            )
						)
					) || (
						!$market->closing_at->isFuture() && (
							$closingRate->firstWhere('currency_id', $item->id)->created_at->isSameDay($market->closing_at)
						)
					)
				)
			) {
				$item->status = true;
			} else {
				$item->status = false;
			}

			return $item;
		});

		return response()->json([
			'data' => $currency->toArray()
		]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

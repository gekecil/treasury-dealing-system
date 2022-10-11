<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\InterbankDeal;
use App\SalesDeal;
use App\NopAdjustment;
use App\SettlementDate;
use App\CurrencyPair;
use App\Currency;

class Nop extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;

		$this->authorizeResource(InterbankDeal::class);
		$this->authorizeResource(SalesDeal::class);
		$this->authorizeResource(NopAdjustment::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$currency = Currency::selectRaw('max(case when secondary_code is null then id else 0 end) as id')
            ->addSelect('primary_code as currency_code')
            ->addSelect(DB::raw('false as is_world_currency'))
            ->addSelect(DB::raw("array_agg(id) as ids"))
            ->where('id', '!=', 1)
            ->groupBy('primary_code')
            ->orderBy('id')
            ->get()
            ->prepend(
				Currency::withTrashed()
                ->select('id')
                ->addSelect('primary_code as currency_code')
                ->addSelect(DB::raw('true as is_world_currency'))
                ->addSelect(DB::raw('array[id] as ids'))
				->find(1)
			);

		$currencyPair = new CurrencyPair;

        $this->request->date_to = ($this->request->input('date_to') ?: Carbon::today()->toDateString());

        while (
            !(
                InterbankDeal::whereDate('created_at', $this->request->date_to)
                ->exists() || (
                    SalesDeal::whereDate('created_at', $this->request->date_to)
                    ->confirmed()
                    ->doesntHave('cancellation')
                    ->exists()
                ) || (
                    NopAdjustment::whereDate('created_at', $this->request->date_to)
                    ->exists()
                )
            ) && (
                InterbankDeal::whereDate('created_at', '<', $this->request->date_to)
                ->exists() || (
                    SalesDeal::whereDate('created_at', '<', $this->request->date_to)
                    ->confirmed()
                    ->doesntHave('cancellation')
                    ->exists()
                ) || (
                    NopAdjustment::whereDate('created_at', '<', $this->request->date_to)
                    ->exists()
                )
            )
        ) {
            $this->request->date_to = Carbon::parse($this->request->date_to)->subDay()->toDateString();
        }

        $nop = $currency->map( function($item, $key) use($currencyPair) {
                $item->opening_adjustment = NopAdjustment::whereDate('created_at', '<', $this->request->date_to)
                    ->whereRaw("currency_id = any ('".$item->ids."')")
                    ->sum('amount');

                $item->current_adjustment = NopAdjustment::whereDate('created_at', $this->request->date_to)
                    ->whereRaw("currency_id = any ('".$item->ids."')")
                    ->sum('amount');

                $item->opening_nop = (
                        InterbankDeal::join($currencyPair->getTable(), function($join) {
                            $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                        })
                        ->where( function($query) {
                            $query->whereDate($query->getModel()->getTable().'.created_at', '<', $this->request->date_to);
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
                            ->where( function($query) {
                                $query->whereDate($query->getModel()->getTable().'.created_at', '<', $this->request->date_to);
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
                            $item->opening_adjustment
                        )
                    );

                $item->current_nop = (
                        $item->opening_nop + (
                            InterbankDeal::join($currencyPair->getTable(), function($join) {
                                $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                            })
                            ->where( function($query) {
                                $query->whereDate($query->getModel()->getTable().'.created_at', $this->request->date_to);
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
                                ->where( function($query) {
                                    $query->whereDate($query->getModel()->getTable().'.created_at', $this->request->date_to);
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
                                $item->current_adjustment
                            )
                        )
                    );

                $item->off_balance_sheet = SettlementDate::whereHas('interbankDeal', function($query) use($currencyPair, $item) {
                        $query->join($currencyPair->getTable(), function($join) {
                            $join->on('currency_pair_id', '=', $join->newQuery()->table.'.id');
                        })
                        ->where( function($query) {
                            $query->whereDate($query->getModel()->getTable().'.created_at', '<=', $this->request->date_to);
                        })
                        ->where( function($query) use($item) {
                            $query->whereRaw("base_currency_id = any ('".$item->ids."')")
                            ->orWhereRaw("counter_currency_id = any ('".$item->ids."')");
                        })
                        ->whereHas('todOrTomOrSpotOrForward', function($query) {
                            $query->whereRaw("lower(name) != 'tod'");
                        });
                    })
                    ->where('value', '>', ($this->request->input('date_to') ?: Carbon::today()->toDateString()))
                    ->get('interbank_deal_id')
                    ->sum( function($settlement) use($currencyPair, $item) {
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

                $item->current_adjustment = $item->opening_adjustment + $item->current_adjustment;

                $item->opening_rate = $item->closingRate()
                    ->firstOrNew(
                        [
                            'created_at' => (
                                    InterbankDeal::whereDate('created_at', $this->request->date_to)->get()
                                    ->concat(
                                        SalesDeal::whereDate('created_at', $this->request->date_to)
                                        ->confirmed()
                                        ->doesntHave('cancellation')
                                        ->get()
                                    )
                                    ->concat(
                                        NopAdjustment::whereDate('created_at', $this->request->date_to)
                                        ->get()
                                    )
                                    ->whenEmpty( function($collection) {
                                        return $collection->push(new InterbankDeal(['base_currency_closing_rate_id' => null]));
                                    })
                                    ->first()
                                    ->baseCurrencyClosingRate()
                                    ->firstOr( function() {
                                        $date = Carbon::parse($this->request->date_to)->subDay();

                                        while ($date->isWeekend()) {
                                            $date = $date->subDay();
                                        }

                                        return (object) (['created_at' => $date]);
                                    })
                                    ->created_at
                                    ->toDateString()
                                )
                        ],
                        [
                            'mid_rate' => null
                        ]
                    )
                    ->mid_rate;

                $item->revaluation_rate = $item->closingRate()
                    ->where('created_at', $this->request->date_to)
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
                    ->mid_rate;

                $item->average_rate = (
                        (
                            $item->opening_rate && $item->current_nop
                        ) ? (
                            (   (
                                    $item->opening_nop * $item->opening_rate
                                ) + (
                                    InterbankDeal::whereDate('created_at', $this->request->date_to)
                                    ->whereHas('currencyPair', function($query) use($item) {
                                        $query->whereRaw("base_currency_id = any ('".$item->ids."')")
                                        ->orWhereRaw("counter_currency_id = any ('".$item->ids."')");
                                    })
                                    ->get()
                                    ->concat(
                                        SalesDeal::whereDate('created_at', $this->request->date_to)
                                        ->whereHas('currencyPair', function($query) use($item) {
                                            $query->whereRaw("base_currency_id = any ('".$item->ids."')")
                                            ->orWhereRaw("counter_currency_id = any ('".$item->ids."')");
                                        })
                                        ->confirmed()
                                        ->doesntHave('cancellation')
                                        ->get()
                                    )
                                    ->concat(
                                        NopAdjustment::whereDate('created_at', $this->request->date_to)
                                        ->whereRaw("currency_id = any ('".$item->ids."')")
                                        ->get()
                                    )
                                    ->sum( function($value) use($item) {
                                        return (
                                            (
                                                (get_class($value) !== NopAdjustment::class) && (
                                                    $value->currencyPair()
                                                    ->whereRaw("not (base_currency_id = any ('".$item->ids."'))")
                                                    ->exists()
                                                ) ? (
                                                    -(floatval($value->amount)) * ($value->customer_rate ?: $value->interoffice_rate)
                                                ) : (
                                                    $value->amount
                                                )
                                            ) * (
                                                
                                                (get_class($value) !== NopAdjustment::class) ? (
                                                    (
                                                        $value->currencyPair()
                                                        ->whereRaw("base_currency_id = any ('".$item->ids."')")
                                                        ->exists()
                                                    ) ? (
                                                        $value->dealRate ? (
                                                            $value->dealRate->base_currency_rate
                                                        ) : (
                                                            $value->interoffice_rate
                                                        )
                                                    ) : (
                                                        $value->dealRate->base_currency_rate / $value->interoffice_rate
                                                    )
                                                ) : (
                                                    $item->opening_rate
                                                )
                                            )
                                        );
                                    })
                                )
                            ) / (
                                $item->current_nop
                            )
                        ) : (
                            null
                        )
                    );

                return $item->makeHidden('ids');
            });

		return response()->json([
			'data' => $nop->toArray()
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

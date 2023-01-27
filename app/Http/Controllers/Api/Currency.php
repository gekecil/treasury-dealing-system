<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Currency as CurrencyModel;
use App\CurrencyPair;
use App\ClosingRate;
use App\Market;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class Currency extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authorizeResource(CurrencyPair::class, 'currencyPair');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = collect([]);

        $market = Market::whereDate('closing_at', Carbon::today()->toDateString())
            ->first();

        $isInterbankDealing = $this->request->input('is_interbank_dealing') && Auth::user()->can('create', 'App\InterbankDeal');

        if (
            $market && (
                (($market->closing_at->isFuture() || $market->closing_at->isToday()) && $isInterbankDealing) || (
                    $market->opening_at->isPast() && $market->closing_at->isFuture()
                )
            )
        ) {
            $currencies = $currencies->concat(CurrencyPair::with(['baseCurrency', 'counterCurrency'])->orderBy('id')->get());
            $currencies = $currencies->map( function($item, $key) use($currencies) {
                    $item->encrypted_query_string = Crypt::encryptString(
                            'id='.$item->id.
                            '&buying_rate='.$item->buying_rate.
                            '&selling_rate='.$item->selling_rate.
                            '&base_currency_buying_rate='.$currencies->where('base_currency_id', $item->base_currency_id)
                                ->whereNull('counter_currency_id')->whenEmpty( function($currencies) {
                                    return $currencies->push(
                                        new CurrencyPair([
                                            'buying_rate' => null,
                                        ])
                                    );
                                })
                                ->first()
                                ->buying_rate.
                            '&base_currency_selling_rate='.$currencies->where('base_currency_id', $item->base_currency_id)
                                ->whereNull('counter_currency_id')->whenEmpty( function($currencies) {
                                    return $currencies->push(
                                        new CurrencyPair([
                                            'selling_rate' => null,
                                        ])
                                    );
                                })
                                ->first()
                                ->selling_rate.
                            '&csrf_token='.$this->request->input('csrf_token')
                        );

                    return $item;
                });
        }

        $data = collect(['currency', 'cross_currency', 'special_currency'])->mapWithKeys( function($items) use($currencies) {
                switch ($items) {
                    case 'currency':
                        $currencies = $currencies->whereNull('counter_currency_id');
                        break;

                    case 'cross_currency':
                        $currencies = $currencies->whereNotNull('counter_currency_id');
                        break;
                }

                $currencies = $currencies->filter( function($item, $key) use($items) {
                    switch ($item->baseCurrency->secondary_code || ($item->counter_currency_id && $item->counterCurrency->secondary_code)) {
                        case true:
                            return $items === 'special_currency';

                        default:
                            return $items !== 'special_currency';
                    }

                })
                ->values();

                return [$items => $currencies];
            });

        $closingRate = ClosingRate::with('currency')
            ->selectRaw('id, currency_id, ((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate');

        if ($market) {
            if ($market->closing_at->isFuture() || $market->closing_at->isToday()) {
                $closingRate->where(
                    'created_at',
                    Market::selectRaw('closing_at::date')
                    ->whereDate('closing_at', '<=', $market->closing_at->today()->toDateString())
                    ->latest('closing_at')
                    ->groupByRaw('closing_at::date')
                    ->skip(1)
                    ->firstOr( function() {
                        $market = new Market(['closing_at' => Carbon::yesterday()]);

                        while ($market->closing_at->isWeekend()) {
                            $market->closing_at = $market->closing_at->subDay();
                        }

                        return $market->fill(['closing_at' => $market->closing_at->toDateString()]);
                    })
                    ->closing_at
                    ->toDateString()
                );

            } else {
                $closingRate->where(
                    'created_at',
                    Market::selectRaw('closing_at::date')
                    ->whereDate('closing_at', '<=', $market->closing_at->toDateString())
                    ->latest('closing_at')
                    ->groupByRaw('closing_at::date')
                    ->skip(1)
                    ->firstOr( function() use($market) {
                        while ($market->closing_at->isWeekend()) {
                            $market->closing_at = $market->closing_at->subDay();
                        }

                        return $market->fill(['closing_at' => $market->closing_at->toDateString()]);
                    })
                    ->closing_at
                    ->toDateString()
                );
            }

        } else {
            $closingRate->where('currency_id', null);
        }

        $closingRate = $closingRate->get()
            ->map( function($item, $key) {
                if ($item->currency_id === 1) {
                    $item->is_world_currency = true;
                }

                return $item;
            });

        $data->put('closing_rate', $closingRate->toArray());
        $data->put('commercial_bank_limit', Auth::user()->commercial_bank_limit);

        return response()->json([
            'data' => $data->toArray(),
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
        $currency = new CurrencyModel;
        $currencyPair = new CurrencyPair;

        collect($request->input('buying_rates'))
        ->each( function($item, $key) use($currency, $currencyPair) {
            $currencyPair = $currencyPair->newQuery()->join($currency->getTable().' as base_currency', 'base_currency_id', '=', 'base_currency.id')
                ->leftJoin($currency->getTable().' as counter_currency', 'counter_currency_id', '=', 'counter_currency.id')
                ->firstWhere(DB::raw(
                        'concat'
                        .'(COALESCE'
                        .'(base_currency.secondary_code, base_currency.primary_code)'
                        .', COALESCE'
                        .'(counter_currency.secondary_code, counter_currency.primary_code)'
                        .')'
                    ),
                    $key
                );

                if ($currencyPair && $currencyPair->fill(['buying_rate' => $item])->isDirty()) {
                    $currencyPair->newQuery()
                    ->where($currencyPair->only(['base_currency_id', 'counter_currency_id']))
                    ->update(['buying_rate' => $item]);
                }
        });

        collect($request->input('selling_rates'))
        ->each( function($item, $key) use($currency, $currencyPair) {
            $currencyPair = $currencyPair->newQuery()->join($currency->getTable().' as base_currency', 'base_currency_id', '=', 'base_currency.id')
                ->leftJoin($currency->getTable().' as counter_currency', 'counter_currency_id', '=', 'counter_currency.id')
                ->firstWhere(DB::raw(
                        'concat'
                        .'(COALESCE'
                        .'(base_currency.secondary_code, base_currency.primary_code)'
                        .', COALESCE'
                        .'(counter_currency.secondary_code, counter_currency.primary_code)'
                        .')'
                    ),
                    $key
                );

                if ($currencyPair && $currencyPair->fill(['selling_rate' => $item])->isDirty()) {
                    $currencyPair->newQuery()
                    ->where($currencyPair->only(['base_currency_id', 'counter_currency_id']))
                    ->update(['selling_rate' => $item]);
                }
        });

        $currencyPair = $currencyPair->select('base_currency_id', 'counter_currency_id', 'updated_at')
            ->whereNotNull('updated_at')
            ->latest('updated_at')
            ->first();

        $xml = new SimpleXMLElement("<?xml version='1.0'?><currencies></currencies>");

        foreach ($currencyPair->only(['base_currency_id', 'counter_currency_id', 'updated_at']) as $key => $value) {
            $xml->addChild($key, $value);
        }

		return response($xml->asXML())->header('Content-Type', 'text/xml');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, CurrencyPair $currencyPair)
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

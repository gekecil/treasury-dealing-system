<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Market as MarketModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Market extends Controller
{
	public function __construct()
    {
		$this->authorizeResource(MarketModel::class, 'market');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $dates = collect($request->input('dates'))
            ->map( function($item) {
                return Carbon::parse($item)->toDateString();
            });

        MarketModel::whereNotIn(DB::raw('closing_at::date'), $dates->toArray())
        ->delete();

        $market = MarketModel::latest()
            ->first();

        $dates->filter( function($item) {
            return !MarketModel::whereDate('closing_at', $item)->exists();
        })
        ->each( function($item) use($market) {
            $carbon = Carbon::parse($item);

            MarketModel::create([
                'user_id' => Auth::id(),
                'opening_at' => $carbon->setTimeFromTimeString(
                        ($market ?: ((object) ['opening_at' => Carbon::now()]))->opening_at
                        ->toTimeString()
                    )
                    ->toDateTimeString(),

                'closing_at' => $carbon->setTimeFromTimeString(
                        ($market ?: ((object) ['closing_at' => Carbon::now()]))->closing_at
                        ->toTimeString()
                    )
                    ->toDateTimeString(),

            ]);
        });

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function show(MarketModel $market)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MarketModel $market)
    {
        $openingAt = $market->opening_at->setTimeFromTimeString($request->input('opening_at'));
        $closingAt = $market->closing_at->setTimeFromTimeString($request->input('closing_at'));

        $inputs = [
                'user_id' => Auth::id(),
                'opening_at' => $openingAt->toDateTimeString(),
                'closing_at' => $closingAt->toDateTimeString(),
            ];

        if ($closingAt->isAfter($openingAt)) {
            $market->update($inputs);

        } else {
            $market = MarketModel::whereDate('closing_at', $closingAt->toDateString())
                ->whereRaw('closing_at <= opening_at');

            if ($market->exists()) {
                $market->update($inputs);

            } else {

                MarketModel::whereDate('closing_at', $closingAt->toDateString())
                ->delete();

                MarketModel::create($inputs);
            }
        }

        return response()->json([
            'status' => 200,
            'data' => MarketModel::latest('updated_at')->first(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Market  $market
     * @return \Illuminate\Http\Response
     */
    public function destroy(MarketModel $market)
    {
        //
    }
}

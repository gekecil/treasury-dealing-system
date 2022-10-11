<?php

namespace App\Http\Controllers;

use App\NopAdjustment;
use App\Currency;
use App\ClosingRate;
use App\Market;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Nop extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(NopAdjustment::class, 'nopAdjustment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::today();

        $currency = Currency::whereHas('closingRate', function($query) use($today) {
                $market = Market::whereDate('opening_at', '<=', $today->toDateString())
                    ->latest('updated_at');

                $market->when(
                    $market->get()->where('opening_at.dayOfYear', $today->dayOfYear)->where('opening_at.year', $today->year)->first(),
                    function($query, $market) {
                        return $query->whereDate('opening_at', '<', $market->opening_at->toDateString());

                    }, function($query) use($today) {
                        return $query->whereDate('opening_at', $today->toDateString());
                    }
                );

                $market = $market->firstOr( function() {
                        $market = new Market;
                        $date = Carbon::yesterday();

                        while ($date->isWeekend()) {
                            $date = $date->subDay();
                        }

                        return $market->fill(['opening_at' => $date->toDateTimeString()]);
                    });

				$query->where('created_at', $market->opening_at->toDateString())
                ->orWhere('created_at', $today->toDateString());
			})
			->orderBy('id')
			->get('primary_code as currency_code');

		return view('nop.index', [
			'currency' => $currency
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
            'currency-code' => 'required|min:3|max:3',
        ]);

        $today = Carbon::today();

		NopAdjustment::create([
			'user_id' => Auth::id(),
			'currency_id' => (
                Currency::withTrashed()
                ->whereNull('secondary_code')
				->firstWhere('primary_code', $request->input('currency-code'))
				->id
            ),

			'base_currency_closing_rate_id' => (
                ClosingRate::where( function($query) use($today) {
                    $market = Market::whereDate('opening_at', '<=', $today->toDateString())
                        ->latest('updated_at');

                    $market->when(
                        $market->get()->where('opening_at.dayOfYear', $today->dayOfYear)->where('opening_at.year', $today->year)->first(),
                        function($query, $market) {
                            return $query->whereDate('opening_at', '<', $market->opening_at->toDateString());

                        }, function($query) use($today) {
                            return $query->whereDate('opening_at', $today->toDateString());
                        }
                    );

                    $market = $market->firstOr( function() {
                            $market = new Market;
                            $date = Carbon::yesterday();

                            while ($date->isWeekend()) {
                                $date = $date->subDay();
                            }

                            return $market->fill(['opening_at' => $date->toDateTimeString()]);
                        });

                    $query->where('created_at', $market->opening_at->toDateString())
                    ->orWhere('created_at', $today->toDateString());
                })
                ->whereHas('currency', function($query) use($request) {
                    $query->where('primary_code', $request->input('currency-code'));
                })
                ->latest()
                ->first()
				->id
            ),

			'amount' => ($request->input('amount') ?: 0),
			'note' => $request->input('note'),
		]);

		Auth::user()->save();

		return redirect()->back()->with('status', 'The NOP Adjustment Was Submitted!');
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
    public function update(Request $request, NopAdjustment $nopAdjustment)
    {
        $request->validate([
            'currency-code' => 'required|min:3|max:3',
        ]);

		$nopAdjustment->fill([
			'user_id' => Auth::id(),

            'currency_id' => (
                Currency::withTrashed()
                ->whereNull('secondary_code')
				->firstWhere('primary_code', $request->input('currency-code'))
				->id
            ),

			'amount' => ($request->input('amount') ?: 0),
			'note' => $request->input('note'),
		])
		->save();

		Auth::user()->save();

		return redirect()->back()->with('status', 'The NOP Adjustment Was Updated!');
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

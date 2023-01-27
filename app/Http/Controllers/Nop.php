<?php

namespace App\Http\Controllers;

use App\NopAdjustment;
use App\Currency;
use App\ClosingRate;
use App\Market;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
				if (Market::whereDate('closing_at', $today->toDateString())->exists()) {
                    $query->where(
                        'created_at',
                        Market::selectRaw('closing_at::date')
                        ->whereDate('closing_at', '<=', $today->toDateString())
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
                    $query->where('id', null);
                }
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
            'base-primary-code' => [
                'required',
                'min:3',
                'max:3',
                Rule::exists((new Currency)->getTable(), 'primary_code')
                ->where(function ($query) use($request) {
                    $query->where('id', ($this->baseCurrencyClosingRate($request)->currency_id));
                }),
            ],
        ]);

        $today = Carbon::today();

		NopAdjustment::create([
			'user_id' => Auth::id(),
			'currency_id' => (
                Currency::withTrashed()
                ->whereNull('secondary_code')
				->firstWhere('primary_code', $request->input('base-primary-code'))
				->id
            ),

			'base_currency_closing_rate_id' => $this->baseCurrencyClosingRate($request)
                ->id,

			'amount' => ($request->input('amount') ?: 0),
			'note' => $request->input('note'),
		]);

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
            'base-primary-code' => [
                'required',
                'min:3',
                'max:3',
                Rule::exists((new Currency)->getTable(), 'primary_code')
                ->where(function ($query) use($request) {
                    $query->where('id', ($this->baseCurrencyClosingRate($request)->currency_id));
                }),
            ],
        ]);

		$nopAdjustment->fill([
			'user_id' => Auth::id(),

            'currency_id' => (
                Currency::withTrashed()
                ->whereNull('secondary_code')
				->firstWhere('primary_code', $request->input('base-primary-code'))
				->id
            ),

			'amount' => ($request->input('amount') ?: 0),
			'note' => $request->input('note'),
		])
		->save();

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

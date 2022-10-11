<?php

namespace App\Http\Controllers;

use App\ClosingRate as ClosingRateModel;
use App\Currency;
use App\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClosingRate extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(ClosingRateModel::class);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $market = Market::whereDate(
                'closing_at',
                '<=',
                Market::whereDate('closing_at', '>', Carbon::today()->toDateString())
                ->orderBy('closing_at')
                ->firstOr( function() {
                    return new Market(['closing_at' => Carbon::today()->toDateString()]);
                })
                ->closing_at
                ->toDateString()
            )
            ->latest('closing_at')
            ->firstOr( function() {
                return new Market(['closing_at' => Carbon::today()->toDateString()]);
            });

        return view('closing-rate.index', [
            'market' => $market,
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
            'currency-code' => [
                'required',
                'exists:App\Currency,primary_code',
            ],
            'next-market-id' => [
                'required',
                'exists:App\Market,id,deleted_at,NULL',
            ],
        ]);

		ClosingRateModel::updateOrCreate(
			[
				'currency_id' => (
                    Currency::withTrashed()
                    ->orderBy('id')
					->firstWhere('primary_code', $request->input('currency-code'))
					->id
                ),

				'created_at' => Market::whereDate('closing_at', '<', Market::find($request->input('next-market-id'))->closing_at->toDateString())
                    ->latest('closing_at')
                    ->firstOr( function() {
                        return new Market(['closing_at' => Carbon::today()->toDateString()]);
                    })
                    ->closing_at
                    ->toDateString(),
			],
			[
				'user_id' => $request->user()->id,
				'buying_rate' => ($request->input('buying-rate') ?: 0),
				'selling_rate' => ($request->input('selling-rate') ?: 0),
			]
		);

		return redirect()->back()->with('status', 'The Closing Rate Was Submitted!');
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

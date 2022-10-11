<?php

namespace App\Http\Controllers;

use App\SalesDeal;
use App\SalesDealFile;
use App\SpecialRateDeal;
use App\Modification;
use App\CurrencyPair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SalesDealConfirmation extends Controller
{
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
		if (
            SalesDealFile::where('sales_deal_id', $id)->exists() && (
                Auth::user()->can('update', SalesDealFile::firstWhere('sales_deal_id', $id))
            )
        ) {
			SalesDealFile::where('sales_deal_id', $id)
			->update([
				'confirmed' => true
			]);
		}

		if (
            SpecialRateDeal::where('sales_deal_id', $id)->exists() && (
                !SalesDeal::find($id)->salesDealRate || (
                    CurrencyPair::whereDate('updated_at', Carbon::today()->toDateString())
                    ->whereNull('counter_currency_id')
                    ->where(SalesDeal::find($id)->buyOrSell->name.'ing_rate', '>', 0)
                    ->whereHas('baseCurrency', function($query) use($id) {
                        $query->where('primary_code', SalesDeal::find($id)->currencyPair->baseCurrency->primary_code);
                    })
                    ->exists()
                )
            ) && (
                Auth::user()->can('update', SpecialRateDeal::firstWhere('sales_deal_id', $id))
            )
        ) {
			SpecialRateDeal::where('sales_deal_id', $id)
			->update([
				'confirmed' => true
			]);

            SalesDeal::find($id)->salesDealRate()->update([
                'base_currency_rate' => (
                        CurrencyPair::whereDate('updated_at', Carbon::today()->toDateString())
                        ->whereNull('counter_currency_id')
                        ->whereHas('baseCurrency', function($query) use($id) {
                            $query->where('primary_code', SalesDeal::find($id)->currencyPair->baseCurrency->primary_code);
                        })
                        ->value(SalesDeal::find($id)->buyOrSell->name.'ing_rate')
                    ),
            ]);
		}

		if (
            Modification::where('deal_updated_id', $id)->exists() && (
                Auth::user()->can('update', Modification::firstWhere('deal_updated_id', $id))
            )
        ) {
			Modification::where('deal_updated_id', $id)
			->update([
				'confirmed' => true
			]);
		}

		return redirect()->route(Str::before($request->input('route-name'), '.').'.index')->with('status', 'The Dealing Was Authorized!');
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

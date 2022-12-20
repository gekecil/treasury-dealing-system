<?php

namespace App\Http\Controllers;

use App\SismontavarDeal as SismontavarDealModel;
use App\CurrencyPair;
use App\SalesDeal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SismontavarDeal extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencyPair = CurrencyPair::whereNull('counter_currency_id')->orderBy('id')->get();

        return view('sismontavar-deal.index', [
            'currencyPair' => $currencyPair
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
        Auth::user()->save();

        $salesDeal = new SalesDeal;
        $salesDeal = $salesDeal->fill([
                'user_id' => Auth::id(),
                'amount' => $request->input('base-volume'),
                'customer_rate' => $request->input('near-rate'),
                'created_at' => Carbon::createFromFormat('Ymd His', $request->input('transaction-date'))->toDateTimeString(),
            ])
            ->forceFill([
                'corporate_name' => $request->input('account-name'),
                'cif' => $request->input('account-cif'),
                'deal_type' => $request->input('deal-type'),
                'direction' => $request->input('direction'),
                'currency_id' => $request->input('currency-pair'),
                'periods' => $request->input('periods'),
                'transaction_purpose' => $request->input('transaction-purpose'),
            ]);

        $this->sismontavar($salesDeal);

		return redirect()->back()->with('status', 'The SISMONTAVAR Data Was Sent!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SismontavarDealModel  $sismontavarDeal
     * @return \Illuminate\Http\Response
     */
    public function show(SismontavarDealModel $sismontavarDeal)
    {
        $this->authorize('view', $sismontavarDeal);

        $sismontavarDeal->makeHidden([
            'sales_deal_id',
            'status_code',
            'status_text',
            'created_at',
            'updated_at',
        ]);

        return view('sismontavar-deal.show', [
			'sismontavarDeal' => $sismontavarDeal,
		]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SismontavarDealModel  $sismontavarDeal
     * @return \Illuminate\Http\Response
     */
    public function edit(SismontavarDealModel $sismontavarDeal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SismontavarDealModel  $sismontavarDeal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SismontavarDealModel $sismontavarDeal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SismontavarDealModel  $sismontavarDeal
     * @return \Illuminate\Http\Response
     */
    public function destroy(SismontavarDealModel $sismontavarDeal)
    {
        //
    }
}

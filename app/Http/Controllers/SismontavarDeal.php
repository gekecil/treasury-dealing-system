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
    public function __construct()
    {
		$this->authorizeResource(SismontavarDealModel::class, 'sismontavarDeal');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencyPair = CurrencyPair::whereNull('counter_currency_id')
            ->whereHas('baseCurrency', function($query) {
                $query->whereNull('secondary_code');
            })
            ->orderBy('id')
            ->get();

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
        $salesDeal = new SalesDeal;

        $salesDeal->fill([
            'user_id' => Auth::id(),
            'currency_pair_id' => $request->input('currency-pair'),
            'amount' => $request->input('base-volume'),
            'created_at' => Carbon::createFromFormat('Ymd His', $request->input('transaction-date'))->toDateTimeString(),
        ])
        ->forceFill([
            'corporate_name' => $request->input('account-name'),
            'cif' => $request->input('account-cif'),
            'deal_type' => $request->input('deal-type'),
            'direction' => $request->input('direction'),
            'periods' => $request->input('periods'),
            'transaction_purpose' => ($request->input('transaction-purpose') ?: null),
            'near_rate' => $request->input('near-rate'),
            'far_rate' => ($request->input('far-rate') ?: null),
            'near_value_date' => $request->input('near-value-date'),
            'far_value_date' => ($request->input('far-value-date') ?: null),
        ]);

        if ($request->has('transaction-id') && $request->filled('transaction-id')) {
            $salesDeal->forceFill(['transaction_id' => $request->input('transaction-id')]);
        }

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
        $sismontavarDeal->makeHidden([
            'sales_deal_id',
            'status_code',
            'status_text',
            'created_at',
            'updated_at',
        ]);

        $currencyPair = CurrencyPair::whereNull('counter_currency_id')
            ->whereHas('baseCurrency', function($query) {
                $query->whereNull('secondary_code');
            })
            ->orderBy('id')
            ->get();

        return view('sismontavar-deal.show', [
			'sismontavarDeal' => $sismontavarDeal,
            'currencyPair' => $currencyPair,
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

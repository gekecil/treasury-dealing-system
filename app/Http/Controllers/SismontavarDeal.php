<?php

namespace App\Http\Controllers;

use App\SismontavarDeal as SismontavarDealModel;
use App\SalesDeal;
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
        return view('sismontavar-deal.index');
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
        $salesDeal = $salesDeal->fill([
                'user_id' => Au
            ]);

        $this->sismontavar();

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

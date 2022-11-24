<?php

namespace App\Http\Controllers;

use App\SismontavarDeal as SismontavarDealModel;
use Illuminate\Http\Request;

class SismontavarDeal extends Controller
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
     * @param  \App\SismontavarDealModel  $sismontavarDeal
     * @return \Illuminate\Http\Response
     */
    public function show(SismontavarDealModel $sismontavarDeal)
    {
        $this->authorize('show', $sismontavarDeal->salesDeal);

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

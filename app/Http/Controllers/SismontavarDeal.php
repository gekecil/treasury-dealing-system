<?php

namespace App\Http\Controllers;

use App\SismontavarDeal as SismontavarDealModel;
use App\Branch;
use Illuminate\Http\Request;
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
        $regions;

		try {
            $regions = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('NamaRegion as region')
                ->where('Company name', 'not like', '%'.strtoupper('(tutup)'))
                ->whereNotNull('NamaRegion')
                ->get();

        } catch (\Exception $e) {
            $regions = Branch::select('region')
                ->whereNotNull('region')
                ->get();
        }

        $regions = $regions->map( function($item) {
                if ($item instanceof Branch) {
                    $item = $item->toArray();
                } else {
                    $item = ((array) $item);
                }

                return ((object) array_map('htmlspecialchars_decode', $item));
            });

        return view('sismontavar-deal.index', [
            'regions' => $regions
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
        $this->authorize('view', $sismontavarDeal);

        $sismontavarDeal->makeHidden([
            'sales_deal_id',
            'status_code',
            'status_text',
            'created_at',
            'updated_at',
            'salesDeal',
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

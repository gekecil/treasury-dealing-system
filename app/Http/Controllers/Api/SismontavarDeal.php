<?php

namespace App\Http\Controllers\Api;

use SismontavarDeal as SismontavarDealModel;
use App\Http\Controllers\Controller;
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
        $sismontavarDeal = SismontavarDealModel::query();
		$recordsTotal = $sismontavarDeal->count();
		
		if ($this->request->filled('search.value')) {
			$sismontavarDeal = $sismontavarDeal->whereRaw("lower(corporate_name) like '".strtolower($this->request->input('search.value'))."%'")
				->orWhereRaw("lower(corporate_name) like '%".strtolower($this->request->input('search.value'))."'")
				->orWhereRaw("lower(corporate_name) like '%".strtolower($this->request->input('search.value'))."%'");
		}
		
		$recordsFiltered = $sismontavarDeal->count();

		if ($this->request->has('start')) {
            $sismontavarDeal->skip($this->request->input('start'));
        }

        if ($this->request->has('length')) {
            $sismontavarDeal->take($this->request->input('length'));
        }

        if ($this->request->has('order')) {
            $order = $this->request->input('order.0');
            $request = $this->request;

            switch ($request->input('columns.'.$order['column'].'.data')) {
                case 'transaction_id':
                    $sismontavarDeal->orderBy('transaction_id', $order['dir']);

                    break;

                case 'corporate_name':
                    $sismontavarDeal->orderBy('corporate_name', $order['dir']);

                    break;

                case 'direction':
                    $sismontavarDeal->orderBy('direction', $order['dir']);

                    break;

                case 'base_currency':
                    $sismontavarDeal->orderBy('base_currency', $order['dir']);

                    break;

                case 'near_rate':
                    $sismontavarDeal->orderBy('near_rate', $order['dir']);

                    break;

                case 'base_volume':
                    $sismontavarDeal->orderBy('base_volume', $order['dir']);

                    break;

                case 'Periods':
                    $sismontavarDeal->orderBy('Periods', $order['dir']);

                    break;

                case 'status_text':
                    $sismontavarDeal->orderBy('status_code', $order['dir']);

                    break;

                default:
                    $sismontavarDeal->orderBy('created_at', $order['dir']);
            }

        } else {
            $sismontavarDeal->latest();
        }

        $sismontavarDeal = $sismontavarDeal->get();

		return response()->json([
			'draw' => $this->request->input('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $sismontavarDeal->toArray()
		]);
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

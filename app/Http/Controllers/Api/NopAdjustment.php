<?php

namespace App\Http\Controllers\Api;

use App\NopAdjustment as NopAdjustmentModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NopAdjustment extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;

        $this->authorizeResource(NopAdjustmentModel::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$nopAdjustment = NopAdjustmentModel::with(['currency' => function($query) {
                $query->select('id', 'primary_code as currency_code');
            }]);

		$recordsTotal = $nopAdjustment->count();
		$recordsFiltered = $recordsTotal;

		if ($this->request->has('start')) {
            $nopAdjustment->skip($this->request->input('start'));
        }

        if ($this->request->has('length')) {
            $nopAdjustment->take($this->request->input('length'));
        }

        if ($this->request->has('order')) {
            $order = $this->request->input('order.0');
            $request = $this->request;

            switch ($request->input('columns.'.$order['column'].'.data')) {
                case 'currency.currency_code':
                    $nopAdjustment->join(
                        $nopAdjustment->getModel()->currency()->getModel()->getTable(),
                        $nopAdjustment->getModel()->currency()->getModel()->getTable().'.id',
                        '=',
                        'currency_id',
                    )
                    ->orderBy('primary_code', $order['dir']);

                    break;

                case 'amount':
                    $nopAdjustment->orderBy('amount', $order['dir']);

                    break;

                case 'note':
                    $nopAdjustment->orderBy('note', $order['dir']);

                    break;

                case 'updated_at':
                    $nopAdjustment->orderBy('updated_at', $order['dir']);

                    break;

                default:
                    $nopAdjustment->orderBy('created_at', $order['dir']);
            }

        } else {
            $nopAdjustment->latest();
        }

        $nopAdjustment = $nopAdjustment->get();

		return response()->json([
			'draw' => $this->request->input('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $nopAdjustment->toArray()
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

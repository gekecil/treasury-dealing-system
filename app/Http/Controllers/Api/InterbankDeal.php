<?php

namespace App\Http\Controllers\Api;

use App\InterbankDeal as InterbankDealModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterbankDeal extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(InterbankDealModel::class);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$interbankDeal = InterbankDealModel::with([
			'counterparty', 'buyOrSell',
			'currencyPair' => function($query) {
					$query->with([
                        'baseCurrency' => function($query) {
                            $query->withTrashed();
                        },
                        'counterCurrency' => function($query) {
                            $query->withTrashed();
                        },
                    ]);
				}
			]);

		$recordsTotal = $interbankDeal->count();

		if ($this->request->filled('search.value')) {
			$interbankDeal->whereHas('counterparty', function($query) {
				$query->whereRaw("lower(name) like '%".strtolower($this->request->input('search.value'))."%'");
			});
		}

		$recordsFiltered = $interbankDeal->count();

		if ($this->request->has('start')) {
            $interbankDeal->skip($this->request->input('start'));
        }

        if ($this->request->has('length')) {
            $interbankDeal->take($this->request->input('length'));
        }

        if ($this->request->has('order')) {
            $order = $this->request->input('order.0');
            $request = $this->request;

            switch ($request->input('columns.'.$order['column'].'.data')) {
                case 'counterparty.name':
                    $interbankDeal->join(
                        $interbankDeal->getModel()->counterparty()->getModel()->getTable(),
                        $interbankDeal->getModel()->counterparty()->getModel()->getTable().'.id',
                        '=',
                        'counterparty_id'
                    )
                    ->orderBy('name', $order['dir']);

                    break;

                case 'currency_pair':
                    $interbankDeal->join(
                        $interbankDeal->getModel()->currencyPair()->getModel()->getTable(),
                        $interbankDeal->getModel()->currencyPair()->getModel()->getTable().'.id',
                        '=',
                        'currency_pair_id'
                    )
                    ->join(
                        $interbankDeal->getModel()->currencyPair()->getModel()->baseCurrency()->getModel()->getTable().' as base_currency',
                        'base_currency.id',
                        '=',
                        'base_currency_id'
                    )
                    ->leftJoin(
                        $interbankDeal->getModel()->currencyPair()->getModel()->counterCurrency()->getModel()->getTable().' as counter_currency',
                        'counter_currency.id',
                        '=',
                        'counter_currency_id'
                    )
                    ->orderBy(DB::raw('CONCAT(base_currency.primary_code, counter_currency.primary_code)'), $order['dir']);

                    break;

                case 'amount':
                    $interbankDeal->orderBy('amount', $order['dir']);

                    break;

                case 'interoffice_rate':
                    $interbankDeal->orderBy('interoffice_rate', $order['dir']);

                    break;

                case 'buy_or_sell.name':
                    $interbankDeal->orderBy('buy_sell', $order['dir']);

                    break;

                case 'basic_remarks':
                    $interbankDeal->orderBy('basic_remarks', $order['dir']);

                    break;

                case 'additional_remarks':
                    $interbankDeal->orderBy('additional_remarks', $order['dir']);

                    break;

                default:
                    $interbankDeal->orderBy('created_at', $order['dir']);
            }

        } else {
            $interbankDeal->latest();
        }

        $interbankDeal = $interbankDeal->get();

		return response()->json([
			'draw' => $this->request->input('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $interbankDeal->toArray()
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

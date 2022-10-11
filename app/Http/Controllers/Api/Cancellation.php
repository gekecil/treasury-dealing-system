<?php

namespace App\Http\Controllers\Api;

use App\Cancellation as CancellationModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Cancellation extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(CancellationModel::class);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$cancellation =	CancellationModel::with(['salesDeal' => function($query) {
			$query->with([
				'account', 'buyOrSell',
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
		}]);

		$user = $this->request->user();

		if ($user->is_branch_office_dealer) {
			$cancellation->whereHas('salesDeal', function($query) use($user) {
				$query->whereHas('branch', function($query) use($user) {
					$query->where('code', $user->branch_code);
				});
			});
		}

		$recordsTotal = $cancellation->count();

		if ($this->request->filled('search.value')) {
			$cancellation->whereHas('salesDeal', function($query) {
				$query->whereHas('account', function($query) {
					$query->whereRaw("lower(name) like '%".strtolower($this->request->input('search.value'))."%'");
				});
			});
		}

		if (filter_input(INPUT_GET, 'is_rejection', FILTER_VALIDATE_BOOLEAN)) {
			$cancellation->whereHas('salesDeal', function($query) {
				$query->whereHas('specialRateDeal', function($query) {
                    $query->where('confirmed', false);
                });
			});

		} else {
			$cancellation->whereHas('salesDeal', function($query) {
				$query->whereDoesntHave('specialRateDeal', function($query) {
                    $query->where('confirmed', false);
                });
			});
        }

		$recordsFiltered = $cancellation->count();

		if ($this->request->has('start')) {
            $cancellation->skip($this->request->input('start'));
        }

        if ($this->request->has('length')) {
            $cancellation->take($this->request->input('length'));
        }

        if ($this->request->has('order')) {
            $order = $this->request->input('order.0');
            $request = $this->request;

            switch ($request->input('columns.'.$order['column'].'.data')) {
                case 'sales_deal.account.name':
                    $cancellation->join(
                        $cancellation->getModel()->salesDeal()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->getTable().'.id',
                        '=',
                        'sales_deal_id'
                    )
                    ->join(
                        $cancellation->getModel()->salesDeal()->getModel()->account()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->account()->getModel()->getTable().'.id',
                        '=',
                        'account_id'
                    )
                    ->orderBy('name', $order['dir']);

                    break;

                case 'sales_deal.currency_pair':
                    $cancellation->join(
                        $cancellation->getModel()->salesDeal()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->getTable().'.id',
                        '=',
                        'sales_deal_id'
                    )
                    ->join(
                        $cancellation->getModel()->salesDeal()->getModel()->currencyPair()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->currencyPair()->getModel()->getTable().'.id',
                        '=',
                        'currency_pair_id'
                    )
                    ->join(
                        $cancellation->getModel()->salesDeal()->getModel()->currencyPair()->getModel()->baseCurrency()->getModel()->getTable().' as base_currency',
                        'base_currency.id',
                        '=',
                        'base_currency_id'
                    )
                    ->leftJoin(
                        $cancellation->getModel()->salesDeal()->getModel()->currencyPair()->getModel()->counterCurrency()->getModel()->getTable().' as counter_currency',
                        'counter_currency.id',
                        '=',
                        'counter_currency_id'
                    )
                    ->orderBy(DB::raw('CONCAT(base_currency.primary_code, counter_currency.primary_code)'), $order['dir']);

                    break;

                case 'sales_deal.amount':
                    $cancellation->join(
                        $cancellation->getModel()->salesDeal()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->getTable().'.id',
                        '=',
                        'sales_deal_id'
                    )
                    ->orderBy('amount', $order['dir']);

                    break;

                case 'sales_deal.customer_rate':
                    $cancellation->join(
                        $cancellation->getModel()->salesDeal()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->getTable().'.id',
                        '=',
                        'sales_deal_id'
                    )
                    ->orderBy('customer_rate', $order['dir']);

                    break;

                case 'sales_deal.interoffice_rate':
                    $cancellation->join(
                        $cancellation->getModel()->salesDeal()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->getTable().'.id',
                        '=',
                        'sales_deal_id'
                    )
                    ->orderBy('interoffice_rate', $order['dir']);

                    break;

                case 'sales_deal.buy_or_sell.name':
                    $cancellation->join(
                        $cancellation->getModel()->salesDeal()->getModel()->getTable(),
                        $cancellation->getModel()->salesDeal()->getModel()->getTable().'.id',
                        '=',
                        'sales_deal_id'
                    )
                    ->orderBy('buy_sell', $order['dir']);

                    break;

                default:
                    $cancellation->orderBy('created_at', $order['dir']);
            }

        } else {
            $cancellation->latest();
        }

        $cancellation = $cancellation->get();

		return response()->json([
			'draw' => $this->request->input('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $cancellation->toArray()
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

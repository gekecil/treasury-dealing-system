<?php

namespace App\Http\Controllers\Api;

use App\SalesDeal as SalesDealModel;
use App\Branch;
use App\Threshold;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesDeal extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(SalesDealModel::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesDeal = SalesDealModel::with([
            'user',
            'branch',
            'account',
            'salesDealFile',
            'specialRateDeal',
            'buyOrSell',
            'ttOrBn',
            'todOrTomOrSpotOrForward',
            'modificationUpdated',
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
			])
            ->doesntHave('cancellation')
            ->select((new SalesDealModel)->getTable().'.*');

        if ($this->request->user()->is_branch_office_dealer) {
            $salesDeal->whereHas('branch', function($query) {
                $query->where('code', $this->request->user()->branch_code);
            });
        }

        $branch = Branch::whereHas('salesDeal', function($query) {
                $query->doesntHave('cancellation');

                if ($this->request->user()->is_branch_office_dealer) {
                    $query->where('code', $this->request->user()->branch_code);
                }
            })
            ->pluck('name', 'code')
            ->unique()
            ->sort();

        $recordsTotal = $salesDeal->count();

        if ($this->request->filled('date_from')) {
            $salesDeal->whereDate('created_at', '>=', $this->request->input('date_from'));
        }

        if ($this->request->filled('date_to')) {
            $salesDeal->whereDate('created_at', '<=', $this->request->input('date_to'));
        }

        if ($this->request->input('is_sales_special_rate_deal')) {
            $salesDeal->has('specialRateDeal');

        } elseif ($this->request->input('is_sales_fx')) {
            $salesDeal->doesntHave('specialRateDeal');
        }

        if ($this->request->filled('search.value')) {
            $salesDeal->whereHas('account', function($query) {
                $query->whereRaw("lower(name) like '%".strtolower($this->request->input('search.value'))."%'")
                ->orWhere('cif', 'like', $this->request->input('search.value').'%');
            });

        } elseif ($this->request->filled('columns.0.search.value')) {
            $salesDeal->whereHas('branch', function($query) {
                $query->where('name', $this->request->input('columns.0.search.value'));
            });
        }

        $recordsFiltered = $salesDeal->count();

        $threshold = Threshold::latest()
			->firstOrNew(
				[],
				['threshold' => null]
			);

        if ($this->request->has('start')) {
            $salesDeal->skip($this->request->input('start'));
        }

        if ($this->request->has('length')) {
            $salesDeal->take($this->request->input('length'));
        }

        if ($this->request->has('order')) {
            $order = $this->request->input('order.0');
            $request = $this->request;

            switch ($request->input('columns.'.$order['column'].'.data')) {
                case 'branch.name':
                    $salesDeal->join(
                        $salesDeal->getModel()->branch()->getModel()->getTable(),
                        $salesDeal->getModel()->branch()->getModel()->getTable().'.id',
                        '=',
                        'branch_id'
                    )
                    ->orderBy('name', $order['dir']);

                    break;

                case 'account.name':
                    $salesDeal->join(
                        $salesDeal->getModel()->account()->getModel()->getTable(),
                        $salesDeal->getModel()->account()->getModel()->getTable().'.id',
                        '=',
                        'account_id'
                    )
                    ->orderBy('name', $order['dir']);

                    break;

                case 'tt_or_bn.name':
                    $salesDeal->orderBy('tt_bn', $order['dir']);

                    break;

                case 'account.cif':
                    $salesDeal->join(
                        $salesDeal->getModel()->account()->getModel()->getTable(),
                        $salesDeal->getModel()->account()->getModel()->getTable().'.id',
                        '=',
                        'account_id'
                    )
                    ->orderBy('cif', $order['dir']);

                    break;

                case 'currency_pair':
                    $salesDeal->join(
                        $salesDeal->getModel()->currencyPair()->getModel()->getTable(),
                        $salesDeal->getModel()->currencyPair()->getModel()->getTable().'.id',
                        '=',
                        'currency_pair_id'
                    )
                    ->join(
                        $salesDeal->getModel()->currencyPair()->getModel()->baseCurrency()->getModel()->getTable().' as base_currency',
                        'base_currency.id',
                        '=',
                        'base_currency_id'
                    )
                    ->leftJoin(
                        $salesDeal->getModel()->currencyPair()->getModel()->counterCurrency()->getModel()->getTable().' as counter_currency',
                        'counter_currency.id',
                        '=',
                        'counter_currency_id'
                    )
                    ->orderBy(DB::raw('CONCAT(base_currency.primary_code, counter_currency.primary_code)'), $order['dir']);

                    break;

                case 'amount':
                    $salesDeal->orderBy('amount', $order['dir']);

                    break;

                case 'customer_rate':
                    $salesDeal->orderBy('customer_rate', $order['dir']);

                    break;

                case 'interoffice_rate':
                    $salesDeal->orderBy('interoffice_rate', $order['dir']);

                    break;

                case 'buy_or_sell.name':
                    $salesDeal->orderBy('buy_sell', $order['dir']);

                    break;

                default:
                    $salesDeal->orderBy('created_at', $order['dir']);
            }

        } else {
            $salesDeal->latest();
        }

		$salesDeal = $salesDeal->get()
            ->append('can_upload_underlying')
            ->map( function($item, $key) {
                $item->baseCurrencyClosingRate = $item->baseCurrencyClosingRate->append('world_currency_closing_mid_rate');
                
                return $item;
            });

		return response()->json([
			'sales_limit' => $this->request->user()->sales_limit,
			'threshold' => $threshold->threshold,
			'branch' => $branch->toArray(),
			'draw' => $this->request->input('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $salesDeal->toArray()
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

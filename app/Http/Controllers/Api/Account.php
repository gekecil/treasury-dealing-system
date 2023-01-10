<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Account as AccountModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Account extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(AccountModel::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $account = new AccountModel;
        $recordsTotal = $account->count();

        if ($this->request->filled('query')) {
            $account = $this->fetch($this->accounts($this->request->input('query'), 10));

            $account = $account->concat(
                    AccountModel::select(['number', 'cif', 'name'])
                    ->whereNotIn('number', $account->pluck('number')->toArray())
                    ->where(DB::raw('concat(number::text, \' \', lower(name))'), 'like', '%'.strtolower($this->request->input('query')).'%')
                    ->orderByRaw('char_length(name)')
                    ->take(1)
                    ->get()
                )
                ->flatMap( function($accounts) {
                    $accounts->monthly_usd_equivalent = AccountModel::firstOrNew(['number' => $accounts->number])->monthly_usd_equivalent;

                    return ((object) collect((array) $accounts)->only(['number', 'cif', 'name', 'monthly_usd_equivalent']));
                });

        } else {
            $account = AccountModel::query();

            if ($this->request->filled('search.value')) {
                $account->where(DB::raw('lower(name)'), 'like', '%'.strtolower($this->request->input('search.value')).'%')
                ->orWhere('cif', 'like', $this->request->input('search.value').'%');
            }

            if ($this->request->has('order')) {
                $order = $this->request->input('order.0');
                $request = $this->request;

                switch ($request->input('columns.'.$order['column'].'.data')) {
                    case 'name':
                        $account = $account->skip($this->request->input('start'))
                        ->take($this->request->input('length'))
                        ->orderBy('name', $order['dir'])
                        ->get();

                        break;

                    case 'number':
                        $account = $account->skip($this->request->input('start'))
                        ->take($this->request->input('length'))
                        ->orderBy('number', $order['dir'])
                        ->get();

                        break;

                    case 'cif':
                        $account = $account->skip($this->request->input('start'))
                        ->take($this->request->input('length'))
                        ->orderBy('cif', $order['dir'])
                        ->get();

                        break;

                    default:
                        switch ($order['dir']) {
                            case 'desc':
                                $account = $account->get()
                                    ->sortByDesc('monthly_usd_equivalent')
                                    ->skip($this->request->input('start'))
                                    ->take($this->request->input('length'))
                                    ->values();

                                break;

                            default:
                                $account = $account->get()
                                    ->sortBy('monthly_usd_equivalent')
                                    ->skip($this->request->input('start'))
                                    ->take($this->request->input('length'))
                                    ->values();
                        }
                }

            } else {
                $account = $account->skip($this->request->input('start'))
                    ->take($this->request->input('length'))
                    ->orderBy('id', 'desc')
                    ->get();
            }

            $account = $account->makeHidden(['salesDeal'])
                ->append(['monthly_usd_equivalent']);

        }

        $recordsFiltered = $account->count();

        return response()->json([
            'draw' => $this->request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $account->toArray(),
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

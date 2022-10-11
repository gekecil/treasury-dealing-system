<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Currency as CurrencyModel;
use App\CurrencyPair;
use App\User;

class Currency extends Controller
{
	public function __construct()
    {
        $this->authorizeResource(CurrencyModel::class, 'currency', [
			'except' => [
				'destroy'
			]
		]);
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$currency = CurrencyPair::whereNull('counter_currency_id')
            ->whereHas('baseCurrency', function($query) {
                $query->whereNull('secondary_code');
            })
			->orderBy('id')
			->get();

		$crossCurrency = CurrencyPair::whereNotNull('counter_currency_id')
            ->whereHas('baseCurrency', function($query) {
                $query->whereNull('secondary_code');
            })
			->orderBy('id')
			->get();

		$specialCurrency = CurrencyPair::whereHas('baseCurrency', function($query) {
                $query->whereNotNull('secondary_code');
            })
			->orderBy('id')
			->get();

		return view('currency.index', [
			'currency' => $currency,
			'crossCurrency' => $crossCurrency,
			'specialCurrency' => $specialCurrency
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
		$currencyPair = CurrencyPair::withoutGlobalScopes()
            ->where( function($query) use($request) {
                $query->whereHas('baseCurrency', function($query) use($request) {
                    $query->where('primary_code', strtoupper($request->input('primary-base-currency-code')))
                    ->where('secondary_code', $request->input('secondary-base-currency-code'));
                });

                if ($request->filled('primary-counter-currency-code')) {
                    $query->whereHas('counterCurrency', function($query) use($request) {
                        $query->where('primary_code', strtoupper($request->input('primary-counter-currency-code')))
                        ->where('secondary_code', $request->input('secondary-counter-currency-code'));
                    });

                } else {
                    $query->whereNull('counter_currency_id');
                }
            });

        $currencyPair = $currencyPair->firstOrNew(
                [],
                ['user_id' => Auth::id()]
            )
            ->fill([
                'belongs_to_interbank' => $request->input('belongs-to-interbank') ?: false,
                'belongs_to_sales' => $request->input('belongs-to-sales') ?: false,
                'dealable_fx_rate' => $request->input('dealable-fx-rate') ?: false,
            ])
            ->forceFill([
                'primary_base_currency_code' => trim(strtoupper($request->input('primary-base-currency-code'))),
                'secondary_base_currency_code' => trim($request->input('secondary-base-currency-code')) ?: null,
                'primary_counter_currency_code' => trim(strtoupper($request->input('primary-counter-currency-code'))) ?: null,
                'secondary_counter_currency_code' => trim($request->input('secondary-counter-currency-code')) ?: null,
            ]);

        if ($currencyPair->trashed()) {
            $currencyPair->restore();
        }

        $currencyPair->save();

        return redirect()->back()->with('status', 'The Currency Was Saved!');
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
    public function destroy(Request $request)
    {
		$deletes = $request->input('deletes');

		collect($deletes)->each( function($item, $key) {
			$currencyPair = CurrencyPair::find($item);

			$this->authorize('delete', $currencyPair);
			$currencyPair->delete();
		});

		return redirect()->back()->with('status', 'The Currency Was Deleted!');
    }
}

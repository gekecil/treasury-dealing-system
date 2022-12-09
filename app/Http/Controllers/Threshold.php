<?php

namespace App\Http\Controllers;

use App\Threshold as ThresholdModel;
use App\SismontavarOption;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Threshold extends Controller
{
	public function __construct()
    {
		$this->authorizeResource(ThresholdModel::class);
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$threshold = ThresholdModel::withCasts(['threshold' => 'float'])->latest();
		$sismontavarOption = SismontavarOption::withCasts(['threshold' => 'float'])->latest();

		return view('threshold.index', [
			'threshold' => $threshold,
			'sismontavarOption' => $sismontavarOption,
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
        $model = new ThresholdModel;

        if ($request->route()->named('settings-sismontavar.store')) {
            $model = new SismontavarOption;
        }

        collect($model->latest()->first()->makeHidden(['id', 'created_at'])->toArray())
        ->merge(['threshold' => ($request->input('threshold') ?: 0)])
        ->merge(['user_id' => Auth::id()])
        ->each( function($item, $key) use($model) {
            $model->{$key} = $item;
        });

        $model->save();

		return redirect()->back()->with('status', 'Threshold Was Saved!');
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

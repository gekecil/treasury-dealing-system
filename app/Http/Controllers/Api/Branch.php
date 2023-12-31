<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Branch as BranchModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class Branch extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(BranchModel::class);
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = $this->branches($this->request->input('region'));
        $branches = $this->fetch($branches)
            ->filter( function($item) {
                return ($item->code && $item->name && $item->region);
            })
            ->sortBy('name')
            ->values()
            ->toArray();

		return response()->json([
			'data' => $branches
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

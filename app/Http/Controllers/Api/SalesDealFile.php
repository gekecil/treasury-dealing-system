<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Rules\Filename;
use App\SalesDealFile as SalesDealFileModel;
use App\SalesDeal;
use App\Branch;

class SalesDealFile extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(SalesDealFileModel::class, 'salesDealFile');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
		$request->validate([
			'document' => ['required', 'file', new Filename],
			'sales_deal_id' => [
				'required',
				Rule::exists((new SalesDeal)->getTable(), 'id')->where(function ($query) {
					if (Auth::user()->is_branch_office_dealer) {
						$query->whereRaw(
							"exists (select * from ".(new Branch)->getTable()." where id = ".
							(new SalesDeal)->getTable().".branch_id and code = '".Auth::user()->branch_code."')"
						);
					}
				}),
			],
		]);
		
		$date = Carbon::now();
		
		$path = $request->file('document')
			->storeAs(
				'uploads/'.$date->format('Y').'/'.$date->format('M'),
				$request->file('document')->getClientOriginalName(),
				'local'
			);
			
		$salesDealFile = SalesDealFileModel::updateOrCreate(
			[
				'sales_deal_id' => $request->input('sales_deal_id')
			],
			[
				'user_id' => Auth::id(),
				'filename' => basename($path),
				'confirmed' => true,
			]
		);
		
		return response()->json([
			'status' => 'The Document Was Saved!',
			'data' => $salesDealFile
		]);
    
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
    public function destroy(SalesDealFileModel $salesDealFile)
    {
		$salesDealFile->update([
			'filename' => null,
			'confirmed' => false,
		]);
		
		return response()->json(['status' => 'The Document Was Deleted!']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Cancellation as CancellationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cancellation extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(CancellationModel::class);
    }
	
	public function index()
    {
		return view('cancellation.index');
    }

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
		$cancellation = CancellationModel::withoutGlobalScopes()->updateOrCreate(
			[
				'sales_deal_id' => $request->input('deal-id')
			],
			[
				'user_id' => Auth::id()
			]
		);

		return redirect()->route('sales-cancellations.edit', [
			'cancellation' => $cancellation->id,
			'is_rejection' => $request->input('is_rejection'),
		])
		->with('status', 'The Cancellation Was Submitted!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CancellationModel $cancellation)
    {
		return view('cancellation.show', [
			'cancellation' => $cancellation
		]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CancellationModel $cancellation)
    {
		return view('cancellation.edit', [
			'cancellation' => $cancellation
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CancellationModel $cancellation)
    {
		$cancellation->update([
			'user_id' => Auth::id(),
			'note' => $request->input('note')
		]);

        if ($request->input('is_rejection')) {
            return redirect()->route('sales-rejections.index')->with('status', 'The Rejection Was Submitted!');
        }

		return redirect()->route('sales-cancellations.index')->with('status', 'The Cancellation Was Submitted!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
		if (collect($request->input('deletes'))->count() >= 50) {
			CancellationModel::truncate();
			
		} else {
			$deletes = $request->input('deletes');
			
			CancellationModel::destroy($deletes);
		}
		
		return redirect()->back()->with('status', 'The Cancellation Was Deleted!');
    }
}

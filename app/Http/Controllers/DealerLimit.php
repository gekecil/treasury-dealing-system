<?php

namespace App\Http\Controllers;

use App\Role;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealerLimit extends Controller
{
	public function __construct()
    {
		$this->authorizeResource(Role::class);
		$this->authorizeResource(Branch::class);
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = $this->branches(strtoupper('kantor pusat'), '!=');
        $branches = $this->fetch($branches)
            ->filter( function($item) {
                return ($item->code && $item->name && $item->region);
            })
            ->map( function($item, $key) {
                $branch = Branch::latest('updated_at')->firstOrNew(
                        [
                            'code' => $item->code
                        ],
                        [
                            'sales_limit' => null,
                            'updated_at' => null,
                        ]
                    );

                $item->sales_limit = $branch->sales_limit;
                $item->updated_at = $branch->updated_at;

                if ($branch->updated_at) {
                    $item->updated_at = $branch->updated_at->toDayDateTimeString();
                }

                return $item;
            })
            ->sortBy('region');

		$role = Role::orderBy('id')->get();

        return view('dealer-limit.index', [
			'role' => $role,
			'branch' => $branches,
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

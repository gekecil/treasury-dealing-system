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
		$branch;

		try {
            $branch = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('Id as code', 'Company name as name', 'NamaRegion as region')
                ->whereRaw("lower(NamaRegion) != 'kantor pusat'")
                ->where('Company name', 'not like', '%'.strtoupper('(tutup)'))
                ->orderBy('NamaRegion')
                ->get()
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

                    if ($branch->updated_at) {
                        $item->updated_at = $branch->updated_at->toDayDateTimeString();
                    }

                    return $item;
                });

        } catch (\Exception $e) {
            $branch = Branch::whereRaw("lower(region) != 'kantor pusat'")
                ->latest('updated_at')
                ->get()
                ->map( function($item, $key) {
                    $item->updated_at = $item->updated_at->toDayDateTimeString();

                    return $item;
                });
        }

        $branch = $branch->map( function($item) {
                if ($item instanceof Branch) {
                    $item = $item->toArray();
                } else {
                    $item = ((array) $item);
                }

                return ((object) array_map('htmlspecialchars_decode', $item));
            });

		$role = Role::orderBy('id')->get();

        return view('dealer-limit.index', [
			'role' => $role,
			'branch' => $branch,
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

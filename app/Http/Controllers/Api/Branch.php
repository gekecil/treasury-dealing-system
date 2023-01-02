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
        $branch;
        $config = config('database.connections.sqlsrv');

        $arguments = collect([
            'Driver' => $config['driver'],
            'Server' => $config['host'],
            'Database' => $config['database'],
            'LoginTimeout' => 5,
        ]);

		try {
            $branch = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('Id as code', 'Company name as name', 'NamaRegion as region')
                ->whereRaw("[Company name] not like '%".strtoupper('(tutup)')."'");

            if ($this->request->has('region')) {
                $branch->where('NamaRegion', $this->request->input('region'));
            }

            $branch = $branch->whereNotNull('NamaRegion')
            ->whereNotNull('RegionKode')
			->distinct()
			->get();

        } catch (\Exception $e) {
            $branch = BranchModel::select('code', 'name', 'region')
                ->whereNotNull('region');
            
            if ($this->request->has('region')) {
                $branch->where('region', $this->request->input('region'));
            }

            $branch = $branch->get();
        }

		return response()->json([
			'data' => $branch->map( function($item) {
                    if ($item instanceof BranchModel) {
                        $item = $item->toArray();
                    } else {
                        $item = ((array) $item);
                    }

                    return ((object) array_map('htmlspecialchars_decode', $item));
                })
                ->toArray()
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

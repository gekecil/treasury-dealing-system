<?php

namespace App\Http\Controllers;

use App\User as UserModel;
use App\Role;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Controller
{
	public function __construct()
    {
		$this->authorizeResource(UserModel::class, 'user', [
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
		return view('user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $regions;

		try {
            $regions = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('NamaRegion as region')
                ->where('Company name', 'not like', '%'.strtoupper('(tutup)'))
                ->whereNotNull('NamaRegion')
                ->get();

        } catch (\Exception $e) {
            $regions = Branch::select('region')
                ->whereNotNull('region')
                ->groupBy('region')
                ->get();
        }

        $regions = $regions->map( function($item) {
                if ($item instanceof Branch) {
                    $item = $item->toArray();
                } else {
                    $item = ((array) $item);
                }

                return ((object) array_map('htmlspecialchars_decode', $item));
            });

		$role = Role::oldest('id')->get();
		
		return view('user.create', [
			'regions' => $regions,
			'role' => $role
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UserModel $user)
    {
        $regions;

		try {
            $regions = DB::connection('sqlsrv')->table('StrukturCabang')
                ->select('Id as branch_code', 'NamaRegion as region', 'Company name as name')
                ->where('Company name', 'not like', '%'.strtoupper('(tutup)'))
                ->whereNotNull('NamaRegion')
                ->orderBy('Company name')
                ->get();

        } catch (\Exception $e) {
            $regions = Branch::select('code as branch_code', 'region', 'name')
                ->whereNotNull('region')
                ->orderBy('name')
                ->get();
        }

        $regions = $regions->map( function($item) {
                if ($item instanceof Branch) {
                    $item = $item->toArray();
                } else {
                    $item = ((array) $item);
                }

                return ((object) array_map('htmlspecialchars_decode', $item));
            });

		$role = Role::oldest('id')->get();
		
		return view('user.edit', [
			'user' => $user,
			'regions' => $regions,
			'role' => $role
		]);
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
			$user = UserModel::find($item);
			
			$this->authorize('delete', $user);
			$user->delete();
		});
		
		if (collect($deletes)->contains(Auth::id())) {
			Auth::user()->delete();
			$request->session()->flush();
		}
		
		return redirect()->back()->with('status', 'The User Was Deleted!');
    }
}

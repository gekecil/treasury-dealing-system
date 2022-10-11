<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User as UserModel;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use PDO;

class User extends Controller
{
	public function __construct(Request $request)
    {
		$this->request = $request;
		$this->authorizeResource(UserModel::class);
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$user = UserModel::with(['branch', 'role']);

		if ($this->request->has('query')) {
			$user->where( function($query) {
				$query->whereRaw("lower(first_name) like '%".strtolower($this->request->input('query'))."'")
				->orWhereRaw("lower(last_name) like '%".strtolower($this->request->input('query'))."'")
				->orWhereRaw("lower(email) like '%".strtolower($this->request->input('query'))."%'");
			})
			->take(10);
		}

		$user = $user->latest('updated_at')->get();

		if ($this->request->has('query')) {
			$user = $user->filter( function($item, $key) {
				return $item->can('create', ($this->request->input('is_interbank_deal') ? 'App\InterbankDeal' : 'App\SalesDeal'));
			})->values();
		}

		return response()->json([
			'data' => $user->makeHidden(['api_token'])->toArray()
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
		$request->validate([
			'email' => [
                'required',
                Rule::unique((new UserModel)->getTable(), 'email')
                ->where( function($query) {
                    $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', Carbon::today()->toDateString());
                })
                ->whereNull('deleted_at'),
            ]
		]);

		if ($request->filled('branch-name')) {
            Branch::updateOrCreate(
                [
                    'code' => $request->input('branch-code'),
                    'name' => $request->input('branch-name'),
                    'region' => $request->input('region'),
                ],
                [
                    'user_id' => Auth::id()
                ]
            );
        }

        $user = UserModel::withoutGlobalScopes()
			->firstOrNew(
				[
					'email' => $request->input('email')
				]
			);

        $user->fill([
            'role_id' => $request->input('role-id'),
            'branch_code' => $request->input('branch-code'),
            'expires_at' => $request->input('expires-at'),
        ]);

		if ($user->exists) {
			$user->restore();

		} else {
			$user->save();
		}

		return response()->json(['redirect' => route('users.index'), 'status' => 'The Users Was Saved!']);
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
    public function update(Request $request, UserModel $user)
    {
		$user->update([
			'role_id' => $request->input('role-id'),
			'branch_code' => $request->input('branch-code'),
            'expires_at' => $request->input('expires-at'),
		]);

		$json =	collect([
			'redirect' => route('users.index'),
			'status' => 'The Users Was Saved!'
		]);

		if ($user->email === Auth::user()->email) {
			$json =	$json->replace(['redirect' => route('dashboard')]);
		}

		return response()->json($json->all());
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

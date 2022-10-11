<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\News as NewsModel;

class News extends Controller
{
    public function __construct(Request $request)
    {
		$this->request = $request;
        $this->authorizeResource(NewsModel::class);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$news = NewsModel::query();
		$recordsTotal = $news->count();
		
		if ($this->request->filled('search.value')) {
			$news = $news->whereRaw("lower(title) like '".strtolower($this->request->input('search.value'))."%'")
				->orWhereRaw("lower(title) like '%".strtolower($this->request->input('search.value'))."'")
				->orWhereRaw("lower(title) like '%".strtolower($this->request->input('search.value'))."%'");
		}
		
		$recordsFiltered = $news->count();

		if ($this->request->has('start')) {
            $news->skip($this->request->input('start'));
        }

        if ($this->request->has('length')) {
            $news->take($this->request->input('length'));
        }

        if ($this->request->has('order')) {
            $order = $this->request->input('order.0');
            $request = $this->request;

            switch ($request->input('columns.'.$order['column'].'.data')) {
                case 'title':
                    $news->orderBy('title', $order['dir']);

                    break;

                case 'updated_at':
                    $news->orderBy('updated_at', $order['dir']);

                    break;

                default:
                    $news->orderBy('created_at', $order['dir']);
            }

        } else {
            $news->latest();
        }

        $news = $news->get();
		
		return response()->json([
			'draw' => $this->request->input('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $news->toArray()
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
     * @param  \Illuminate\Http\Request  request()
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
     * @param  \Illuminate\Http\Request  request()
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

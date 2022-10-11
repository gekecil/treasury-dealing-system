<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\News as NewsModel;
use App\User;

class News extends Controller
{
    public function __construct()
    {
		$this->authorizeResource(NewsModel::class, 'news', [
			'except' => [
				'viewAny', 'destroy'
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
        $this->authorize('create', NewsModel::class);

		return view('news.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		return view('news.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		NewsModel::create([
			'user_id' => Auth::id(),
			'title' => e($request->input('title')),
			'description' => e($request->input('description')),
			'content' => preg_replace('/\r|\n/', '', $request->input('content'))
		]);
		
		return redirect()->route('news.index')->with('status', 'News Was Added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(NewsModel $news)
    {
		return view('news.show', [
			'news' => $news
		]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(NewsModel $news)
    {
		return view('news.edit', [
			'news' => $news
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NewsModel $news)
    {
		$news->update([
			'user_id' => Auth::id(),
			'title' => e($request->input('title')),
			'description' => e($request->input('description')),
			'content' => preg_replace('/\r|\n/', '', $request->input('content'))
		]);
		
		return redirect()->route('news.index')->with('status', 'The News Was Updated!');
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
			$news = NewsModel::find($item);
			
			$this->authorize('delete', $news);
			$news->delete();
		});
		
		return redirect()->back()->with('status', 'The News Was Deleted!');
    }
}

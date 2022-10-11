<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;

class Search extends Controller
{
    public function __invoke(Request $request)
    {
		$this->authorize('viewAny', News::class);
		
		$results = News::selectRaw("id, title as text, 'news.show' as route")
			->whereRaw("lower(title) like '".strtolower($request->input('query'))."%'")
			->orWhereRaw("lower(title) like '%".strtolower($request->input('query'))."'")
			->orWhereRaw("lower(title) like '%".strtolower($request->input('query'))."%'")
			->latest()
			->get();
			
		return view('search', [
			'results' => $results
		]);
    }
}

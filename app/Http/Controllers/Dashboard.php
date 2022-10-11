<?php

namespace App\Http\Controllers;

use App\SalesDeal;
use App\News;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __invoke(Request $request)
    {
		$this->authorize('viewAny', SalesDeal::class);

        $salesDeal = SalesDeal::selectRaw("currency_pair_id, buy_sell, extract(month from created_at) as month, count(*) as count")
            ->confirmed()
            ->doesntHave('cancellation')
            ->whereYear('created_at', SalesDeal::latest()->firstOrNew([], ['created_at' => Carbon::today()->toDateString()])->created_at->year)
            ->groupByRaw("currency_pair_id, buy_sell, extract(month from created_at)")
            ->with(['currencyPair' => function($query) {
                $query->select(['id', 'base_currency_id']);
            }])
            ->get();

		$news = News::limit(50)->latest()->get();

		return view('dashboard', [
			'salesDeal' => $salesDeal,
			'news' => $news,
		]);
    }
}

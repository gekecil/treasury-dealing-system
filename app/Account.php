<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Account extends Model
{
	use SoftDeletes;
	
	protected $guarded = ['id'];
	public $timestamps = false;

	public function getMonthlyUsdEquivalentAttribute()
    {
		return $this->query()->where('cif', $this->cif)->get()->flatMap( function($items) {
				$items = $items->salesDeal()->select('currency_pair_id', 'base_currency_closing_rate_id', 'amount', 'buy_sell')
					->whereDoesntHave('currencyPair', function($query) {
						$query->whereNotNull('counter_currency_id');
					})
					->whereHas('buyOrSell', function($query) {
						$query->select('id')->where('name', 'sell');
					})
					->where('created_at', '>', Carbon::today()->startOfMonth())
					->get();
					
				return $items;
			})
			->sum('usd_equivalent');
    }
	
	public function salesDeal()
    {
        return $this->hasMany(SalesDeal::class)
			->confirmed()
			->doesntHave('cancellation');
    }
}

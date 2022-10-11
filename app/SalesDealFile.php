<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\SalesDealFile as SalesDealFileObserver;

class SalesDealFile extends Model
{
    protected $guarded = ['id'];

    public function salesDeal()
    {
        return $this->belongsTo(SalesDeal::class);
    }
	
	public function user()
    {
        return $this->belongsTo(User::class)
			->withoutGlobalScopes();
    }
	
	protected static function booted()
	{
		static::observe(SalesDealFileObserver::class);
	}
}

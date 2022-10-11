<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NopAdjustment extends Model
{
	protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'currency_id',
    ];
	
	public function currency()
    {
        return $this->belongsTo(Currency::class)
			->withTrashed();
    }
	
	public function baseCurrencyClosingRate()
    {
        return $this->belongsTo(ClosingRate::class, 'base_currency_closing_rate_id')
			->selectRaw('((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate, created_at');
    }
	
}

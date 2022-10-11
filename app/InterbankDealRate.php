<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterbankDealRate extends Model
{
	protected $primaryKey = 'interbank_deal_id';
	protected $guarded = [];
	public $timestamps = false;

    public function counterCurrencyClosingRate()
    {
        return $this->belongsTo(ClosingRate::class, 'counter_currency_closing_rate_id')
			->selectRaw('((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate, created_at');
    }
}

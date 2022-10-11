<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialRateDeal extends Model
{
    const UPDATED_AT = 'created_at';

	protected $primaryKey = 'sales_deal_id';
	protected $guarded = [];

	public function salesDeal()
    {
        return $this->belongsTo(SalesDeal::class);
    }
}

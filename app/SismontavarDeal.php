<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SismontavarDeal extends Model
{
    protected $primaryKey = 'sales_deal_id';
    protected $guarded = [];

    public $timestamps = false;

	public function salesDeal()
    {
        return $this->belongsTo(SalesDeal::class);
    }
}

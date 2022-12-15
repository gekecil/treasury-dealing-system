<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SismontavarDeal extends Model
{
    protected $primaryKey = 'transaction_id';
    protected $guarded = [];

	public function salesDeal()
    {
        return $this->belongsTo(SalesDeal::class);
    }
}

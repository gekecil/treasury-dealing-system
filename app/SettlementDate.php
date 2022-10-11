<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettlementDate extends Model
{
	protected $guarded = ['id'];
	public $timestamps = false;

	public function interbankDeal()
    {
        return $this->belongsTo(InterbankDeal::class);
    }
}

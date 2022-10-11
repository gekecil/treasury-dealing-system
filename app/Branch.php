<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
	protected $guarded = ['id'];

	public function salesDeal()
    {
        return $this->hasMany(SalesDeal::class)
			->confirmed()
			->doesntHave('cancellation');
    }
}

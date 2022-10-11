<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
	use SoftDeletes;

	protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];

    public function getWorldCurrencyCodeAttribute()
	{
		return $this->query()->withTrashed()->find(1)->currency_code;
	}

	public function closingRate()
    {
        return $this->hasMany(ClosingRate::class)
			->selectRaw('currency_id, created_at, ((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate');
    }
}

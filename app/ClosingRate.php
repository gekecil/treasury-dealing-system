<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClosingRate extends Model
{
	protected $guarded = ['id'];
	public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'currency_id',
    ];

	protected $casts = [
        'created_at' => 'date',
    ];

    public function getWorldCurrencyClosingMidRateAttribute()
	{
		return (
			$this->query()->selectRaw('((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate')
            ->where('created_at', $this->created_at)
            ->firstWhere('currency_id', 1)
            ->mid_rate
		);
	}

	public function currency()
    {
        return $this->belongsTo(Currency::class)
            ->withTrashed();
    }
}

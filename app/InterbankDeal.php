<?php

namespace App;

use App\Observers\InterbankDeal as InterbankDealObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InterbankDeal extends Model
{
	protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'currency_pair_id',
    ];

	public function getBranchPlAttribute()
    {
		return (
			abs(round(floatval(
				($this->customer_rate - $this->interoffice_rate) * (
					$this->amount
				) * (
					$this->currency->cross_currency_code ? (
						$this->cross_currency_closing_rate
					) : (1)
				)
			), 2))
		);
	}

	public function counterparty()
    {
        return $this->belongsTo(Counterparty::class);
    }

	public function currencyPair()
    {
        return $this->belongsTo(CurrencyPair::class)
			->withoutGlobalScopes();
    }

	public function user()
    {
        return $this->belongsTo(User::class)
			->withoutGlobalScopes();
    }

	public function baseCurrencyClosingRate()
    {
        return $this->belongsTo(ClosingRate::class, 'base_currency_closing_rate_id')
			->selectRaw('currency_id, created_at, ((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate');
    }

	public function buyOrSell()
    {
		return $this->belongsTo(Group::class, 'buy_sell', 'name_id')
			->where('group', 'buy_sell');
    }

	public function todOrTomOrSpotOrForward()
    {
		return $this->belongsTo(Group::class, 'tod_tom_spot_forward', 'name_id')
			->where('group', 'tod_tom_spot_forward');
    }

	public function interbankDealRate()
    {
        return $this->hasOne(InterbankDealRate::class);
    }

	public function settlementDate()
    {
        return $this->hasOne(SettlementDate::class);
    }

	public function modification()
    {
        return $this->hasOne(Modification::class, 'deal_updated_id')
			->whereHas('interbankOrSales', function($query) {
				$query->where('name', 'interbank');
			});
    }

    public function dealRate()
    {
        return $this->interbankDealRate();
    }

    protected static function booted()
    {
        static::observe(InterbankDealObserver::class);
    }
}

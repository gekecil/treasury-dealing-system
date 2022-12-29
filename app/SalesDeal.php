<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\SalesDealScope;
use App\Observers\SalesDeal as SalesDealObserver;

class SalesDeal extends Model
{
	const UPDATED_AT = null;

	protected $guarded = ['id'];

	protected $appends = [
		'monthly_usd_equivalent'
	];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'currency_pair_id',
    ];

	public function scopeConfirmed($query)
    {
		$query = $query->where( function($query) {
			$query->whereDoesntHave('specialRateDeal', function($query) {
				$query->where('confirmed', false);
			})
			->whereDoesntHave('modificationUpdated', function($query) {
				$query->where('confirmed', false);
			})
			->whereDoesntHave('salesDealFile', function($query) {
				$query->where('confirmed', false);
			});
		});
		
        return $query;
    }

	public function getIsConfirmedAttribute()
	{
		return (
			(!$this->specialRateDeal || $this->specialRateDeal->confirmed) &&
			(!$this->modificationUpdated || $this->modificationUpdated->confirmed) &&
			(!$this->salesDealFile || $this->salesDealFile->confirmed)
		);
	}

	public function getCanUploadUnderlyingAttribute()
	{
		return (($this->buyOrSell->name === 'sell') && !$this->currencyPair->counter_currency_id);
	}

	public function getMonthlyUsdEquivalentAttribute()
    {
		return $this->query()->select('currency_pair_id', 'base_currency_closing_rate_id', 'amount', 'buy_sell')
			->confirmed()
			->doesntHave('cancellation')
            ->whereHas('account', function($query) {
				$query->where('cif', $this->account->cif);
			})
            ->whereHas('buyOrSell', function($query) {
                $query->select('id')->where('name', 'sell');
            })
            ->whereDoesntHave('currencyPair', function($query) {
                $query->whereNotNull('counter_currency_id');
            })
			->whereBetween('created_at', [$this->created_at->startOfMonth()->toDateTimeString(), $this->created_at->toDateTimeString()])
			->get()
			->sum('usd_equivalent');
    }

	public function getUsdEquivalentAttribute()
    {
		return (
			abs(round(floatval(
				$this->baseCurrencyClosingRate->mid_rate * (
					$this->amount
				) / (
					$this->baseCurrencyClosingRate->world_currency_closing_mid_rate
				)
			), 2))
		);
	}

	public function getBranchPlAttribute()
    {
		return (
			abs(round(floatval(
				($this->customer_rate - $this->interoffice_rate) * (
					$this->amount
				) * (
					$this->salesDealRate ? (
						$this->salesDealRate->counterCurrencyClosingRate->mid_rate
						
					) : (1)
				)
			), 2))
		);
	}

	public function getBlotterNumberAttribute()
    {
        $transactions = $this->newQuery()
            ->select(['user_id', 'created_at'])
            ->whereDate('created_at', $this->created_at->toDateString())
            ->oldest()
            ->orderBy('id')
            ->get();

        $transactions = $transactions->map( function($item) {
                return [
                    'trader_id' => ((int) preg_replace('/\s+/', '', $item->user->nik)),
                    'transaction_date' => $item->created_at->format('Ymd His')
                ];
            });

        $transactions = $transactions->concat(
                SismontavarDeal::select(['trader_id', 'transaction_date'])
                ->where('transaction_date', 'like', $this->created_at->format('Ymd').'%')
                ->get()
                ->reject( function($item) use($transactions) {
                    foreach ($transactions->toArray() as $value) {
                        if ($value === $item->toArray()) {
                            return true;
                        }
                    }

                    return false;
                })
                ->toArray()
            )
            ->sortBy('transaction_date')
            ->values();

        $search = [
                'trader_id' => ((int) preg_replace('/\s+/', '', $this->user->nik)),
                'transaction_date' => $this->created_at->format('Ymd His')
            ];

        $blotterNumber = $transactions->search( function($item) use($search) {
                return ($item === $search);
            });

        if ($blotterNumber === false) {
            $blotterNumber = $transactions->count();
        }

        return (
            substr('00'.(string) ($blotterNumber +1), -3)
        );
	}

	public function getFxSrAttribute()
    {
        return ($this->specialRateDeal()->exists() ? 'SR' : 'FX');
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

	public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
	
	public function account()
    {
        return $this->belongsTo(Account::class)
			->withTrashed();
    }

	public function baseCurrencyClosingRate()
    {
        return $this->belongsTo(ClosingRate::class, 'base_currency_closing_rate_id')
			->selectRaw('id, currency_id, created_at, ((buying_rate + selling_rate) / 2)::numeric(16, 2) as mid_rate');
    }

	public function buyOrSell()
    {
		return $this->belongsTo(Group::class, 'buy_sell', 'name_id')
			->where('group', 'buy_sell');
    }

	public function ttOrBn()
    {
		return $this->belongsTo(Group::class, 'tt_bn', 'name_id')
			->where('group', 'tt_bn');
    }

	public function todOrTomOrSpotOrForward()
    {
		return $this->belongsTo(Group::class, 'tod_tom_spot_forward', 'name_id')
			->where('group', 'tod_tom_spot_forward');
    }

	public function lhbuRemarksCode()
    {
		return $this->belongsTo(Group::class, 'lhbu_remarks_code', 'name_id')
			->where('group', 'lhbu_remarks_code');
    }

	public function lhbuRemarksKind()
    {
		return $this->belongsTo(Group::class, 'lhbu_remarks_kind', 'name_id')
			->where('group', 'lhbu_remarks_kind');
    }

	public function otherLhbuRemarksKind()
    {
		return $this->hasOne(OtherLhbuRemarksKind::class);
    }

	public function salesDealRate()
    {
        return $this->hasOne(SalesDealRate::class);
    }

	public function salesDealFile()
    {
        return $this->hasOne(SalesDealFile::class);
    }

	public function specialRateDeal()
    {
        return $this->hasOne(SpecialRateDeal::class);
    }

	public function modificationCreated()
    {
        return $this->hasOne(Modification::class, 'deal_created_id')
			->whereHas('interbankOrSales', function($query) {
				$query->where('name', 'sales');
			});
    }

	public function modificationUpdated()
    {
        return $this->hasOne(Modification::class, 'deal_updated_id')
			->whereHas('interbankOrSales', function($query) {
				$query->where('name', 'sales');
			});
    }

	public function cancellation()
    {
        return $this->hasOne(Cancellation::class);
    }

    public function dealRate()
    {
        return $this->salesDealRate();
    }

	protected static function booted()
    {
        static::addGlobalScope(new SalesDealScope);
        static::observe(SalesDealObserver::class);
    }
}

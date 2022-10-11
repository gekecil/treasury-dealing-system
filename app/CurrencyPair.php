<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use App\Scopes\CurrencyPairScope;
use App\Observers\CurrencyPair as CurrencyPairObserver;

class CurrencyPair extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];

	public function resolveRouteBinding($value, $field = null)
	{
        if (Route::current()->getPrefix() === 'api') {
			$value = $this->get()->filter( function($item, $key) use($value) {
                    if (!$item->counter_currency_id) {
                        $item->counterCurrency = new Currency([
                            'primary_code' => null,
                            'secondary_code' => null,
                        ]);
                    }

                    return (
                        ($item->baseCurrency->primary_code.$item->counterCurrency->primary_code === $value) && (
                            !$item->baseCurrency->secondary_code
                        ) && (
                            !$item->counterCurrency->secondary_code
                        )
                    );
                })
                ->whenEmpty( function($collection) {
                    return $collection->push(
                        $this->forceFill(['id' => null])
                    );
                })
                ->first()->id;
		}

		return $this->findOrFail($value);
	}

	public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function counterCurrency()
    {
        return $this->belongsTo(Currency::class, 'counter_currency_id');
    }

    public function salesDeal()
    {
        return $this->hasMany(SalesDeal::class)
			->confirmed()
			->doesntHave('cancellation');
    }

    protected static function booted()
    {
        static::addGlobalScope(new CurrencyPairScope);
        static::observe(CurrencyPairObserver::class);
    }
}

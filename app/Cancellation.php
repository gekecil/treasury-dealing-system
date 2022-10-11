<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use App\Scopes\CancellationScope;

class Cancellation extends Model
{
	const UPDATED_AT = 'created_at';

	protected $guarded = ['id'];

	public function salesDeal()
    {
        return $this->belongsTo(SalesDeal::class);
    }

	public function resolveRouteBinding($value, $field = null)
	{
        if (collect(['sales-cancellations.edit', 'sales-cancellations.update'])->contains(Route::currentRouteName())) {
            return $this->withoutGlobalScopes()->findOrFail($value);
        }

        return $this->findOrFail($value);
	}

	protected static function booted()
    {
        static::addGlobalScope(new CancellationScope);
    }
}

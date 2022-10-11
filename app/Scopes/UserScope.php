<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Token;
use Carbon\Carbon;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
		$builder->join(Token::__callStatic('getTable', []), $model->getTable().'.id', '=', Token::__callStatic('getTable', []).'.user_id')
			->where( function($query) {
                $query->whereNull('expires_at')
                ->orWhere('expires_at', '>=', Carbon::today()->toDateString());
            })
            ->select($model->getTable().'.*', 'api_token');
    }
}

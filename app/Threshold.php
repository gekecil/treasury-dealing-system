<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
	const UPDATED_AT = 'created_at';
	protected $guarded = ['id'];

	public function user()
    {
        return $this->belongsTo(User::class)
			->withoutGlobalScopes();
    }
}

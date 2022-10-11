<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
	protected $primaryKey = 'user_id';

	protected $guarded = [
	    'id',
	];

	protected $hidden = [
        'api_token',
    ];

	public function user()
    {
        return $this->belongsTo(User::class);
    }
}

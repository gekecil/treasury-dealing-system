<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $guarded = ['id'];
	public $timestamps = false;
	
	public function limit()
    {
        return $this->hasOne(Limit::class);
    }
}

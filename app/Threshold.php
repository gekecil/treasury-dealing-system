<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
	const UPDATED_AT = 'created_at';
	protected $guarded = ['id'];
}

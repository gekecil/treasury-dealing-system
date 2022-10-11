<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Market extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'opening_at' => 'datetime',
        'closing_at' => 'datetime',
    ];
}

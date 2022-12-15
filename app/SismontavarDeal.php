<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SismontavarDeal extends Model
{
    protected $primaryKey = 'transaction_id';
    protected $keyType = 'string';
    protected $guarded = [];

    public $incrementing = false;

}

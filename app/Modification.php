<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modification extends Model
{
	const UPDATED_AT = 'created_at';
	
	protected $guarded = ['id'];
	
	public function user()
    {
        return $this->belongsTo(User::class)
			->withoutGlobalScopes();
    }
	
	public function salesDealCreated()
    {
        return $this->belongsTo(SalesDeal::class, 'deal_created_id');
    }
	
	public function salesDealUpdated()
    {
        return $this->belongsTo(SalesDeal::class, 'deal_updated_id');
    }
	
	public function interbankOrSales()
    {
		return $this->belongsTo(Group::class, 'interbank_sales', 'name_id')
			->where('group', 'interbank_sales');
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
		'App\Account' => 'App\Policies\AccountPolicy',
		'App\Branch' => 'App\Policies\BranchPolicy',
		'App\Cancellation' => 'App\Policies\CancellationPolicy',
		'App\CurrencyPair' => 'App\Policies\CurrencyPairPolicy',
		'App\ClosingRate' => 'App\Policies\ClosingRatePolicy',
		'App\InterbankDeal' => 'App\Policies\InterbankDealPolicy',
		'App\Market' => 'App\Policies\MarketPolicy',
		'App\Modification' => 'App\Policies\ModificationPolicy',
		'App\NopAdjustment' => 'App\Policies\NopAdjustmentPolicy',
		'App\News' => 'App\Policies\NewsPolicy',
		'App\Role' => 'App\Policies\RolePolicy',
		'App\SalesDealFile' => 'App\Policies\SalesDealFilePolicy',
		'App\SalesDeal' => 'App\Policies\SalesDealPolicy',
		'App\SpecialRateDeal' => 'App\Policies\SpecialRateDealPolicy',
		'App\Threshold' => 'App\Policies\ThresholdPolicy',
		'App\User' => 'App\Policies\UserPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}

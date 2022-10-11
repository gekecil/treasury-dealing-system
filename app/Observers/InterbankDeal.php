<?php

namespace App\Observers;
use App\InterbankDealRate;
use App\ClosingRate;

use App\InterbankDeal as InterbankDealModel;

class InterbankDeal
{
    /**
     * Handle the interbank deal as interbank deal model "created" event.
     *
     * @param  \App\InterbankDeal as InterbankDealModel  $interbankDeal
     * @return void
     */
    public function created(InterbankDealModel $interbankDeal)
    {
        if ($interbankDeal->currencyPair->counter_currency_id) {
            InterbankDealRate::create([
                'interbank_deal_id' => $interbankDeal->id,

                'counter_currency_closing_rate_id' => (
                    ClosingRate::where('created_at', $interbankDeal->baseCurrencyClosingRate->created_at->toDateString())
                    ->firstWhere('currency_id', $interbankDeal->currencyPair->counter_currency_id)
                    ->id
                ),

                'base_currency_rate' => 0,
            ]);
        }
    }

    /**
     * Handle the interbank deal as interbank deal model "updated" event.
     *
     * @param  \App\InterbankDeal as InterbankDealModel  $interbankDeal
     * @return void
     */
    public function updated(InterbankDealModel $interbankDeal)
    {
        //
    }

    /**
     * Handle the interbank deal as interbank deal model "deleted" event.
     *
     * @param  \App\InterbankDeal as InterbankDealModel  $interbankDeal
     * @return void
     */
    public function deleted(InterbankDealModel $interbankDeal)
    {
        //
    }

    /**
     * Handle the interbank deal as interbank deal model "restored" event.
     *
     * @param  \App\InterbankDeal as InterbankDealModel  $interbankDeal
     * @return void
     */
    public function restored(InterbankDealModel $interbankDeal)
    {
        //
    }

    /**
     * Handle the interbank deal as interbank deal model "force deleted" event.
     *
     * @param  \App\InterbankDeal as InterbankDealModel  $interbankDeal
     * @return void
     */
    public function forceDeleted(InterbankDealModel $interbankDeal)
    {
        //
    }
}

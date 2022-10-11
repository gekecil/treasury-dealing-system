<?php

namespace App\Observers;

use App\SalesDeal as SalesDealModel;
use App\SalesDealRate;
use App\ClosingRate;

class SalesDeal
{
    /**
     * Handle the sales deal as sales deal model "created" event.
     *
     * @param  \App\SalesDeal as SalesDealModel  $salesDeal
     * @return void
     */
    public function created(SalesDealModel $salesDeal)
    {
        if ($salesDeal->currencyPair->counter_currency_id) {
            SalesDealRate::create([
                'sales_deal_id' => $salesDeal->id,

                'counter_currency_closing_rate_id' => (
                    ClosingRate::where('created_at', $salesDeal->baseCurrencyClosingRate->created_at->toDateString())
                    ->firstWhere('currency_id', $salesDeal->currencyPair->counter_currency_id)
                    ->id
                ),

                'base_currency_rate' => 0,
            ]);
        }
    }

    /**
     * Handle the sales deal as sales deal model "updated" event.
     *
     * @param  \App\SalesDeal as SalesDealModel  $salesDeal
     * @return void
     */
    public function updated(SalesDealModel $salesDeal)
    {
        //
    }

    /**
     * Handle the sales deal as sales deal model "deleted" event.
     *
     * @param  \App\SalesDeal as SalesDealModel  $salesDeal
     * @return void
     */
    public function deleted(SalesDealModel $salesDeal)
    {
        //
    }

    /**
     * Handle the sales deal as sales deal model "restored" event.
     *
     * @param  \App\SalesDeal as SalesDealModel  $salesDeal
     * @return void
     */
    public function restored(SalesDealModel $salesDeal)
    {
        //
    }

    /**
     * Handle the sales deal as sales deal model "force deleted" event.
     *
     * @param  \App\SalesDeal as SalesDealModel  $salesDeal
     * @return void
     */
    public function forceDeleted(SalesDealModel $salesDeal)
    {
        //
    }
}

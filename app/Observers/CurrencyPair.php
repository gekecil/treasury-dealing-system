<?php

namespace App\Observers;

use App\CurrencyPair as CurrencyPairModel;
use App\Currency;

class CurrencyPair
{
    /**
     * Handle the currency pair "creating" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function creating(CurrencyPairModel $currencyPair)
    {
        $baseCurrency = Currency::withTrashed()
        ->updateOrCreate([
            'primary_code' => $currencyPair->primary_base_currency_code,
            'secondary_code' => $currencyPair->secondary_base_currency_code,
        ]);

        if ($baseCurrency->trashed()) {
            $baseCurrency->restore();
        }

        if ($currencyPair->primary_counter_currency_code) {
            $counterCurrency = Currency::withTrashed()
            ->updateOrCreate([
                'primary_code' => $currencyPair->primary_counter_currency_code,
                'secondary_code' => $currencyPair->secondary_counter_currency_code,
            ]);

            if ($counterCurrency->trashed()) {
                $counterCurrency->restore();
            }

            $currencyPair->counter_currency_id = $counterCurrency->id;
        }

        $currencyPair->setRawAttributes(
            $currencyPair->makeHidden([
                'baseCurrency',
                'primary_base_currency_code',
                'secondary_base_currency_code',
                'counterCurrency',
                'primary_counter_currency_code',
                'secondary_counter_currency_code',
                'updated_at',
            ])->toArray()
        )
        ->fill([
            'base_currency_id' => $baseCurrency->id,
            'counter_currency_id' => $currencyPair->counter_currency_id,
        ]);
    }

    /**
     * Handle the currency pair "created" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function created(CurrencyPairModel $currencyPair)
    {
        //
    }

    /**
     * Handle the currency pair "updating" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function updating(CurrencyPairModel $currencyPair)
    {
        $this->creating($currencyPair);
    }

    /**
     * Handle the currency pair "updated" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function updated(CurrencyPairModel $currencyPair)
    {
        //
    }

    /**
     * Handle the currency pair "deleted" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function deleted(CurrencyPairModel $currencyPair)
    {
        Currency::whereNotIn('id', (
            \App\CurrencyPair::all()->flatMap( function($items) {
                return array_values($items->only(['base_currency_id', 'counter_currency_id']));
            })
            ->filter()
            ->toArray()
        ))
        ->delete();
    }

    /**
     * Handle the currency pair "restored" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function restored(CurrencyPairModel $currencyPair)
    {
        //
    }

    /**
     * Handle the currency pair "force deleted" event.
     *
     * @param  \App\CurrencyPair  $currencyPair
     * @return void
     */
    public function forceDeleted(CurrencyPairModel $currencyPair)
    {
        //
    }
}

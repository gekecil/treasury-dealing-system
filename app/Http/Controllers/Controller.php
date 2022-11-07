<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sismontavar($salesDeal)
    {
		try {
            if ($salesDeal->usd_equivalent >= 250000) {
                DB::connection('pgsql_sismontavar')
                ->table('bi_transaction_data')
                ->updateOrInsert(
                    [
                        'trader_id' => $salesDeal->user_id,
                        'transaction_date' => $salesDeal->created_at->format('Ymd His'),
                    ],
                    [
                        'transaction_id' => (($salesDeal->specialRateDeal ? 'SR' : 'FX').$salesDeal->created_at->format('dmY').'-'.substr(
                                    '00'.(string) (
                                        $salesDeal->newQuery()
                                        ->confirmed()
                                        ->doesntHave('cancellation')
                                        ->whereDate('created_at', $salesDeal->created_at->toDateString())
                                        ->whereTime('created_at', '<=', $salesDeal->created_at->toTimeString())
                                        ->count()
                                    ), -3
                                )
                            ),

                        'corporate_name' => $salesDeal->account->name,
                        'platform' => 'TDS',
                        'deal_type' => ucwords($salesDeal->todOrTomOrSpotOrForward->name),
                        'direction' => ucwords($salesDeal->buyOrSell->name),
                        'base_currency' => $salesDeal->currencyPair->baseCurrency->primary_code,
                        'quote_currency' => $salesDeal->currencyPair->counterCurrency()->firstOrNew([], ['primary_code' => 'IDR'])->primary_code,
                        'near_rate' => $salesDeal->customer_rate,
                        'confirmed_by' => $salesDeal->specialRateDeal()->firstOrNew([], ['user_id' => $salesDeal->user_id])->user->full_name,
                        'trader_name' => $salesDeal->user->full_name,
                        'transaction_purpose' => (
                                substr('0'.((string) $salesDeal->lhbu_remarks_code), -2).' '.substr('00'.((string) $salesDeal->lhbu_remarks_kind), -3)
                            ),

                        'near_value_date' => $salesDeal->created_at->format('Ymd His'),
                        'confirmed_at' => $salesDeal->specialRateDeal()->firstOrNew([], ['created_at' => $salesDeal->created_at])
                            ->created_at
                            ->format('Ymd His'),

                        'corporate_id' => $salesDeal->account_id,
                        'transaction_status' => 1,
                    ]
                );
            }

        } catch (\Exception $e) {
            //
        }
    }
}

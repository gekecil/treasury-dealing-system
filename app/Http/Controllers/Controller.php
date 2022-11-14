<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sismontavar($salesDeal)
    {
		try {
            if ($salesDeal->usd_equivalent >= 250000) {
                $corporateName = $salesDeal->account->name;

                while (strlen($corporateName) > 56) {
                    $corporateName = Str::beforeLast($corporateName, ' ');
                }

                $traderName = $salesDeal->user->full_name;

                while (strlen($traderName) > 20) {
                    $traderName = Str::beforeLast($traderName, ' ');
                }

                $confirmedBy = $salesDeal->specialRateDeal()->firstOrNew([], ['user_id' => $salesDeal->user_id])->user->full_name;

                while (strlen($confirmedBy) > 30) {
                    $confirmedBy = Str::beforeLast($confirmedBy, ' ');
                }

                DB::connection('pgsql_sismontavar')
                ->table('bi_transaction_data')
                ->updateOrInsert(
                    [
                        'trader_id' => preg_replace('/\s+/', '', $salesDeal->user->nik),
                        'transaction_date' => $salesDeal->created_at->format('Ymd His'),
                    ],
                    [
                        'transaction_id' => (($salesDeal->specialRateDeal()->exists() ? 'SR' : 'FX').$salesDeal->created_at->format('dmy').substr(
                                    '00'.(string) (
                                        $salesDeal->newQuery()
                                        ->whereDate('created_at', $salesDeal->created_at->toDateString())
                                        ->whereTime('created_at', '<=', $salesDeal->created_at->toTimeString())
                                        ->count()
                                    ), -3
                                )
                            ),

                        'corporate_name' => $corporateName,
                        'platform' => 'TDS',
                        'deal_type' => ucwords($salesDeal->todOrTomOrSpotOrForward->name),
                        'direction' => ucwords($salesDeal->buyOrSell->name),
                        'base_currency' => $salesDeal->currencyPair->baseCurrency->primary_code,
                        'quote_currency' => $salesDeal->currencyPair->counterCurrency()->firstOrNew([], ['primary_code' => 'IDR'])->primary_code,
                        'base_volume' => abs($salesDeal->amount),
                        'quote_volume' => ($salesDeal->customer_rate * abs($salesDeal->amount)),
                        'periode' => 0,
                        'near_rate' => $salesDeal->customer_rate,
                        'far_rate' => null,
                        'confirmed_by' => $confirmedBy,
                        'trader_name' => $traderName,
                        'transaction_purpose' => (
                                substr('0'.((string) $salesDeal->lhbu_remarks_code), -2).' '.substr('00'.((string) $salesDeal->lhbu_remarks_kind), -3)
                            ),

                        'reported' => false,
                        'near_value_date' => $salesDeal->created_at->format('Ymd His'),
                        'confirmed_at' => $salesDeal->specialRateDeal()->firstOrNew([], ['created_at' => $salesDeal->created_at])
                            ->created_at
                            ->format('Ymd His'),

                        'corporate_id' => substr($salesDeal->account->cif, 3),
                        'created_at' => $salesDeal->created_at->now()->toDateTimeString(),
                        'manual' => false,
                        'transaction_status' => 1,
                    ]
                );
            }

        } catch (\Exception $e) {
            //
        }
    }
}

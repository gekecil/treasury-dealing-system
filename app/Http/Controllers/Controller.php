<?php

namespace App\Http\Controllers;

use App\SismontavarDeal;
use App\SismontavarOption;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sismontavar($salesDeal)
    {
        $sismontavarOption = SismontavarOption::latest()
            ->first();

		if (!$salesDeal->currencyPair->counter_currency_id && ($salesDeal->usd_equivalent >= $sismontavarOption->threshold)) {
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

            $sismontavarDeal = new SismontavarDeal;
            $sismontavarDeal->fill([
                'sales_deal_id' => $salesDeal->id,
                'transaction_id' => (($salesDeal->specialRateDeal()->exists() ? 'SR' : 'FX').$salesDeal->created_at->format('dmy').substr(
                            '00'.(string) (
                                $salesDeal->newQuery()
                                ->whereDate('created_at', $salesDeal->created_at->toDateString())
                                ->whereTime('created_at', '<=', $salesDeal->created_at->toTimeString())
                                ->count()
                            ), -3
                        )
                    ),

                'transaction_date' => $salesDeal->created_at->format('Ymd His'),
                'corporate_id' => substr($salesDeal->account->cif, -4),
                'corporate_name' => $corporateName,
                'platform' => 'TDS',
                'deal_type' => ucwords($salesDeal->todOrTomOrSpotOrForward->name),
                'direction' => ucwords($salesDeal->buyOrSell->name),
                'base_currency' => $salesDeal->currencyPair->baseCurrency->primary_code,
                'quote_currency' => $salesDeal->currencyPair->counterCurrency()->firstOrNew([], ['primary_code' => 'IDR'])->primary_code,
                'base_volume' => abs($salesDeal->amount),
                'quote_volume' => ($salesDeal->customer_rate * abs($salesDeal->amount)),
                'periods' => (
                        collect([
                            'TOD' => 0,
                            'TOM' => 1,
                            'spot' => 2,
                            'forward' => 3,
                        ])
                        ->get($salesDeal->todOrTomOrSpotOrForward->name)
                    ),

                'near_rate' => $salesDeal->customer_rate,
                'near_value_date' => $salesDeal->created_at->format('Ymd His'),
                'confirmed_at' => $salesDeal->specialRateDeal()
                    ->firstOrNew([], ['created_at' => $salesDeal->created_at])
                    ->created_at
                    ->format('Ymd His'),

                'confirmed_by' => $confirmedBy,
                'trader_id' => preg_replace('/\s+/', '', $salesDeal->user->nik),
                'trader_name' => $traderName,
                'transaction_purpose' => (
                        substr('0'.((string) $salesDeal->lhbu_remarks_code), -2).' '.substr('00'.((string) $salesDeal->lhbu_remarks_kind), -3)
                    ),
            ]);

            foreach ($sismontavarDeal->toArray() as $key => $value) {
                if ($key === '') {
                    $sismontavarDeal->{$key} = preg_replace("/[^A-Za-z0-9\ ]/", "", $value);
                } else {
                    $sismontavarDeal->{$key} = preg_replace("/(\!|\#|\$|\%|\^|\&|\*|\'|\(|\)|\?|\/|\;|\<|\>)/", "", $value);
                }
            }

            try {
                $token = Http::asForm()
                    ->post(env('SISMONTAVAR_URL_ACCESS_TOKEN'), [
                        'grant_type' => 'client_credentials',
                        'client_id' => env('SISMONTAVAR_CLIENT_ID'),
                        'client_secret' => env('SISMONTAVAR_CLIENT_SECRET'),
                        'scope' => 'sismontavar',
                    ])
                    ->json();

                $http = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'X-BI-CLIENT-ID' => env('SISMONTAVAR_CLIENT_ID'),
                        'X-BI-CLIENT-SECRET' => env('SISMONTAVAR_CLIENT_SECRET'),
                    ])
                    ->withToken($token['access_token'])
                    ->withBody(
                        json_encode([
                            'Username' => $sismontavarOption->username,
                            'SandiBank' => $sismontavarOption->bank_id,
                            'Data' => [[
                                'Transaction_ID' => $sismontavarDeal->transaction_id,
                                'Transaction_Date' => $sismontavarDeal->transaction_date,
                                'Corporate_ID' => $sismontavarDeal->corporate_id,
                                'Corporate_Name' => $sismontavarDeal->corporate_name,
                                'Platform' => $sismontavarDeal->platform,
                                'Deal_Type' => $sismontavarDeal->deal_type,
                                'Direction' => $sismontavarDeal->direction,
                                'Base_Currency' => $sismontavarDeal->base_currency,
                                'Quote_Currency' => $sismontavarDeal->quote_currency,
                                'Base_Volume' => $sismontavarDeal->base_volume,
                                'Quote_Volume' => $sismontavarDeal->quote_volume,
                                'Periods' => $sismontavarDeal->periods,
                                'Near_Rate' => $sismontavarDeal->near_rate,
                                'Near_Value_Date' => $sismontavarDeal->near_value_date,
                                'Confirmed_At' => $sismontavarDeal->confirmed_at,
                                'Confirmed_By' => $sismontavarDeal->confirmed_by,
                                'Trader_ID' => $sismontavarDeal->trader_id,
                                'Trader_Name' => $sismontavarDeal->trader_name,
                                'Transaction_Purpose' => $sismontavarDeal->transaction_purpose,
                            ]],
                        ]),

                        'application/json'
                    )
                    ->post(env('SISMONTAVAR_URL_SEND_DATA'));

                if ($http->ok()) {
                    $sismontavarDeal->fill([
                        'status_code' => $http->status(),
                    ])
                    ->save();

                } else {
                    $sismontavarDeal->fill([
                        'status_code' => $http->status(),
                        'status_text' => $http->body(),
                    ])
                    ->save();
                }

            } catch (\Exception $e) {
                if (!$sismontavarDeal->exists) {
                    $sismontavarDeal->fill(['status_code' => 500]);
                }

                $sismontavarDeal->fill(['status_text' => $e->getMessage()])
                ->save();
            }
        }
    }
}

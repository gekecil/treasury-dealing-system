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

		if (
            !$salesDeal->exists || (
                !$salesDeal->currencyPair->counter_currency_id && ($salesDeal->usd_equivalent >= $sismontavarOption->threshold)
            )
        ) {
            $corporateName = $salesDeal->account()->firstOrNew([], ['name' => $salesDeal->corporate_name])->name;
            $corporateName = preg_replace("/[^A-Za-z0-9\ ]/", "", $corporateName);

            while (strlen($corporateName) > 56) {
                $corporateName = Str::beforeLast($corporateName, ' ');
            }

            $traderName = preg_replace("/[^A-Za-z\ ]/", "", Str::before($salesDeal->user->full_name, '@'));

            while (strlen($traderName) > 20) {
                $traderName = Str::beforeLast($traderName, ' ');
            }

            $confirmedBy = $salesDeal->specialRateDeal()->firstOrNew([], ['user_id' => $salesDeal->user_id])->user->full_name;
            $confirmedBy = preg_replace("/[^A-Za-z\ ]/", "", Str::before($confirmedBy, '@'));

            while (strlen($confirmedBy) > 30) {
                $confirmedBy = Str::beforeLast($confirmedBy, ' ');
            }

            $transactionId = $salesDeal->transaction_id;

            if (!$transactionId) {
                $transactionId = (($salesDeal->fx_sr).($salesDeal->created_at->format('dmy')).($salesDeal->blotter_number));
            }

            $sismontavarDeal = SismontavarDeal::select('transaction_id')
                ->firstOrNew(
                    ['transaction_id' => $transactionId],
                    []
                );

            $sismontavarDeal->transaction_date = $salesDeal->created_at->format('Ymd His');
            $sismontavarDeal->corporate_id = substr($salesDeal->account()->firstOrNew([], ['cif' => $salesDeal->cif])->cif, -4);
            $sismontavarDeal->corporate_name = $corporateName;
            $sismontavarDeal->platform = 'TDS';
            $sismontavarDeal->deal_type = ucwords($salesDeal->todOrTomOrSpotOrForward()->firstOrNew([], ['name' => $salesDeal->deal_type])->name);
            $sismontavarDeal->direction = ucwords($salesDeal->buyOrSell()->firstOrNew([], ['name' => $salesDeal->direction])->name);
            $sismontavarDeal->base_currency = $salesDeal->currencyPair
                ->baseCurrency
                ->primary_code;

            $sismontavarDeal->quote_currency = 'IDR';
            $sismontavarDeal->base_volume = abs($salesDeal->amount);
            $sismontavarDeal->quote_volume = (($salesDeal->customer_rate ?: $salesDeal->near_rate) * abs($salesDeal->amount));
            $sismontavarDeal->periods = (
                    collect([
                        'TOD' => 0,
                        'TOM' => 1,
                        'spot' => 2,
                        'forward' => 3,
                    ])
                    ->filter( function($value, $key) use($salesDeal) {
                        return ($key === $salesDeal->todOrTomOrSpotOrForward()->firstOrNew([], ['name' => $salesDeal->deal_type])->name);
                    })
                    ->whenEmpty( function($collection) use($salesDeal) {
                        return $collection->push($salesDeal->periods);
                    })
                    ->first()
                );

            $sismontavarDeal->near_rate = ($salesDeal->near_rate ?: $salesDeal->customer_rate);
            $sismontavarDeal->near_value_date = ($salesDeal->near_value_date ?: $salesDeal->created_at->format('Ymd His'));
            $sismontavarDeal->confirmed_at = $salesDeal->specialRateDeal()
                ->firstOrNew([], ['created_at' => $salesDeal->created_at])
                ->created_at
                ->format('Ymd His');

            $sismontavarDeal->confirmed_by = $confirmedBy;
            $sismontavarDeal->trader_id = preg_replace('/\s+/', '', $salesDeal->user->nik);
            $sismontavarDeal->trader_name = $traderName;
            $sismontavarDeal->transaction_purpose = ($salesDeal->transaction_purpose ?: (
                    substr('0'.((string) $salesDeal->lhbu_remarks_code), -2).' '.substr('00'.((string) $salesDeal->lhbu_remarks_kind), -3)
                ));

            if ($salesDeal->far_rate) {
                $sismontavarDeal->far_rate = $salesDeal->far_rate;
            }

            if ($salesDeal->far_value_date) {
                $sismontavarDeal->far_value_date = $salesDeal->far_value_date;
            }

            foreach ($sismontavarDeal->makeHidden(['status_code', 'status_text', 'created_at', 'updated_at'])->toArray() as $key => $value) {
                $sismontavarDeal->{$key} = preg_replace("/(\!|\#|\$|\%|\^|\&|\*|\'|\(|\)|\?|\/|\;|\<|\>)/", "", $value);
            }

            if ($sismontavarDeal->transaction_id > 999) {
                $sismontavarDeal->status_code = 0;

                $sismontavarDeal->save();

                $sismontavarDeal->transaction_id = (($salesDeal->fx_sr).($salesDeal->created_at->format('dmy')).($salesDeal->blotter_number));

                $sismontavarDeal->save();
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
                            'Data' => [
                                collect(
                                    $sismontavarDeal->makeHidden(['status_code', 'status_text', 'created_at', 'updated_at'])->toArray()
                                )
                                ->mapWithKeys( function($item, $key) {
                                    $key = Str::of($key)->replaceMatches('/_id$/', function($match) {
                                            return strtoupper($match[0]);
                                        })
                                        ->ucfirst();

                                    return [((string) $key) => $item];
                                })
                                ->toArray()
                            ],
                        ]),

                        'application/json'
                    )
                    ->post(env('SISMONTAVAR_URL_SEND_DATA'));

                $sismontavarDeal->status_code = $http->status();
                $sismontavarDeal->status_text = $http->body();

                $sismontavarDeal->save();

            } catch (\Exception $e) {
                if (!$sismontavarDeal->exists || !$sismontavarDeal->status_code) {
                    $sismontavarDeal->status_code = 500;
                }

                $sismontavarDeal->status_text = $e->getMessage();
                
                $sismontavarDeal->save();
            }
        }
    }
}

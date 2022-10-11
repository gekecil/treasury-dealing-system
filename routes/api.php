<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')
    ->group(function () {

    Route::apiResource('interbank-deals', 'Api\InterbankDeal')
    ->only([
        'index'
    ]);

    Route::apiResource('nop', 'Api\Nop')
    ->only([
        'index'
    ]);

    Route::apiResource('nop-adjustments', 'Api\NopAdjustment')
    ->only([
        'index'
    ]);

    Route::apiResource('sales-deals', 'Api\SalesDeal')
    ->only([
        'index', 'show'
    ]);

    Route::apiResource('sales-deal-file', 'Api\SalesDealFile')
    ->only([
        'store', 'destroy'
    ])
    ->parameters([
        'sales-deal-file' => 'salesDealFile'
    ]);

    Route::apiResource('blotter', 'Api\Blotter')
    ->only([
        'index',
    ]);

    Route::apiResource('closing-rates', 'Api\ClosingRate')
    ->only([
        'index'
    ]);

    Route::apiResource('markets', 'Api\Market')
    ->only([
        'store', 'update'
    ]);

    Route::apiResource('accounts', 'Api\Account')
    ->only([
        'index', 'show'
    ]);

    Route::apiResource('cancellations', 'Api\Cancellation')
    ->only([
        'index', 'show'
    ]);

    Route::apiResource('currencies', 'Api\Currency')
    ->only([
        'index', 'store'
    ])
    ->parameters([
        'currencies' => 'currencyPair'
    ]);

    Route::apiResource('news', 'Api\News')
    ->only([
        'index'
    ]);

    Route::apiResource('users', 'Api\User')
    ->only([
        'index', 'store', 'update'
    ]);

    Route::apiResource('branches', 'Api\Branch')
    ->only([
        'index'
    ]);

    Route::post('image-upload', 'Api\ImageUpload')
    ->name('image-upload')
    ->middleware('can:create, App\News');

});

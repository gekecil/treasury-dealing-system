<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/dashboard');

Route::middleware(['oauth2', 'refresh.token'])->group( function() {

	Route::get('dashboard', 'Dashboard')
	->middleware('can:viewAny,App\SalesDeal')
	->name('dashboard');

	Route::get('markets', 'Market')
    ->name('market');

	Route::get('profile', 'Profile')
	->name('profile');

	Route::get('search', 'Search')
	->middleware('can:viewAny,App\News')
	->name('search');

	Route::get('sign-out', 'Sign@out')
	->name('sign-out');

	Route::resource('interbank-dealing', 'InterbankDeal')
	->parameters([
		'interbank-dealing' => 'interbankDeal'
	]);

	Route::resource('interbank-nop', 'Nop')
	->parameters([
		'interbank-nop' => 'nopAdjustment'
	]);

	Route::resource('sales-fx', 'SalesDeal')
	->except([
		'create', 'destroy'
	])
	->parameters([
		'sales-fx' => 'salesDeal'
	]);

	Route::resource('sales-request-for-fx-deal', 'SalesDeal')
	->except([
		'create', 'destroy'
	])
	->parameters([
		'sales-request-for-fx-deal' => 'salesDeal'
	])
	->names([
		'index' => 'sales-special-rate-deal.index',
		'store' => 'sales-special-rate-deal.store',
		'show' => 'sales-special-rate-deal.show',
		'edit' => 'sales-special-rate-deal.edit',
		'update' => 'sales-special-rate-deal.update'
	]);

	Route::resource('sales-cancellations', 'Cancellation')
	->only([
		'index', 'store', 'edit', 'update', 'show'
	])
	->parameters([
		'sales-cancellations' => 'cancellation'
	]);

	Route::resource('sales-rejections', 'Rejection')
	->only([
		'index', 'store', 'edit', 'update', 'show'
	])
	->parameters([
		'sales-rejections' => 'cancellation'
	]);

	Route::resource('sales-blotter', 'SalesDeal')
	->except([
		'create', 'destroy'
	])
	->parameters([
		'sales-blotter' => 'salesDeal'
	]);

    Route::resource('sales-top-ten-obox', 'SalesDeal')
	->only([
		'index'
	])
	->parameters([
		'sales-top-ten-obox' => 'salesDeal'
	]);

	Route::resource('sales-deal-file', 'SalesDealFile')
	->only([
		'show'
	])
	->parameters([
		'sales-deal-file' => 'salesDealFile'
	]);

	Route::resource('sales-deal-confirmation', 'SalesDealConfirmation')
	->only([
		'update'
	])
	->parameters([
		'sales-deal-confirmation' => 'salesDealConfirmation'
	]);

	Route::resource('sismontavar-deals', 'SismontavarDeal')
	->only([
		'store', 'show'
	])
	->parameters([
		'sismontavar-deals' => 'sismontavarDeal'
	]);

	Route::post('interbank-nop-excel', 'NopExcel')->name('interbank-nop.excel');

	Route::post('sales-blotter-excel', 'SalesBlotterExcel')->name('sales-blotter.excel');

	Route::resource('currencies', 'Currency');

	Route::resource('closing-rates', 'ClosingRate')
	->only([
		'index', 'store'
	])
	->parameters([
		'closing-rates' => 'closingRate'
	]);

	Route::resource('accounts', 'Account');

	Route::resource('news', 'News');

	Route::resource('users', 'User');

	Route::resource('settings-threshold', 'Threshold')
	->only([
		'index', 'store'
	])
	->parameters([
		'settings-threshold' => 'threshold'
	]);

	Route::resource('settings-sismontavar', 'Threshold')
	->only([
		'store'
	])
	->parameters([
		'settings-sismontavar' => 'threshold'
	]);

	Route::resource('settings-dealer-limits', 'DealerLimit')
	->only([
		'index'
	]);

	Route::resource('settings-roles', 'Role')
	->only([
		'store'
	]);

	Route::resource('settings-branches', 'Branch')
	->only([
		'store'
	]);

    Route::group([
        'prefix' => 'ftp-curve-data',
        'as' => 'fcd.'
    ], function () {
        Route::get('/', 'FcdController@index')->name('index');
        Route::post('/table', 'FcdController@fcdTable')->name('table');
        Route::post('/upload', 'FcdController@fcdUpload')->name('upload');
        Route::post('/delete', 'FcdController@fcdDelete')->name('delete');
        Route::get('/download/{id?}', 'FcdController@convertExcel')->name('download');
    });
});

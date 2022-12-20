<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('test', function () {
    dd(
        (new App\SalesDeal(['user_id' => 1, 'created_at' => \Carbon\Carbon::createFromFormat('Ymd His', '20221220 151515')->toDateTimeString()]))
        ->blotter_number
    );
});

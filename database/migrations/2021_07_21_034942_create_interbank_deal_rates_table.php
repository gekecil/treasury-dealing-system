<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterbankDealRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interbank_deal_rates', function (Blueprint $table) {
            $table->foreignId('interbank_deal_id')->primary()->constrained();
            $table->foreignId('counter_currency_closing_rate_id')->constrained('closing_rates');
            $table->decimal('base_currency_rate', 16, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interbank_deal_rates');
    }
}

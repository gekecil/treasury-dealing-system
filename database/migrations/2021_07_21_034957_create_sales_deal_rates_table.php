<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDealRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_deal_rates', function (Blueprint $table) {
            $table->foreignId('sales_deal_id')->primary()->constrained();
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
        Schema::dropIfExists('sales_deal_rates');
    }
}

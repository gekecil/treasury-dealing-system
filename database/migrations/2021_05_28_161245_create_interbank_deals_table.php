<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterbankDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interbank_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('counterparty_id')->constrained();
            $table->foreignId('currency_pair_id')->constrained();
            $table->foreignId('base_currency_closing_rate_id')->constrained('closing_rates');
            $table->decimal('interoffice_rate', 32, 12);
            $table->decimal('amount', 16, 2);
            $table->integer('tod_tom_spot_forward');
            $table->integer('buy_sell');
            $table->text('basic_remarks')->nullable();
            $table->text('additional_remarks')->nullable();
            $table->timestamps(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interbank_deals');
    }
}

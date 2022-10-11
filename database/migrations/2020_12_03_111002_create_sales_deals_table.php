<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('currency_pair_id')->constrained();
            $table->foreignId('base_currency_closing_rate_id')->constrained('closing_rates');
            $table->decimal('interoffice_rate', 16, 4);
            $table->decimal('customer_rate', 16, 4);
            $table->decimal('amount', 16, 2);
            $table->integer('tod_tom_spot_forward');
            $table->integer('tt_bn');
            $table->integer('buy_sell');
            $table->integer('lhbu_remarks_code');
            $table->integer('lhbu_remarks_kind');
            $table->timestamp('created_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_deals');
    }
}

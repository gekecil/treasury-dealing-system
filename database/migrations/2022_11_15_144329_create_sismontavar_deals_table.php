<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSismontavarDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sismontavar_deals', function (Blueprint $table) {
            $table->string('transaction_id', 11)->primary();
            $table->string('transaction_date', 15);
            $table->integer('corporate_id');
            $table->string('corporate_name', 56);
            $table->string('platform', 25);
            $table->string('deal_type', 8);
            $table->string('direction', 12);
            $table->string('base_currency', 3);
            $table->string('quote_currency', 3);
            $table->decimal('base_volume', 16, 2);
            $table->decimal('quote_volume', 16, 2);
            $table->integer('periods');
            $table->decimal('near_rate', 16, 4);
            $table->decimal('far_rate', 16, 4)->nullable();
            $table->string('near_value_date', 15);
            $table->string('far_value_date', 15)->nullable();
            $table->string('confirmed_at', 15);
            $table->string('confirmed_by', 30);
            $table->bigInteger('trader_id');
            $table->string('trader_name', 20);
            $table->string('transaction_purpose', 40);
            $table->integer('status_code');
            $table->text('status_text')->nullable();
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
        Schema::dropIfExists('sismontavar_deals');
    }
}

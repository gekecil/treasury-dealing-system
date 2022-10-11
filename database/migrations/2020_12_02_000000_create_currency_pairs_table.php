<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_pairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('base_currency_id')->constrained('currencies');
            $table->unsignedBigInteger('counter_currency_id')->nullable();
            $table->decimal('buying_rate', 16, 4)->nullable();
            $table->decimal('selling_rate', 16, 4)->nullable();
            $table->boolean('belongs_to_sales')->default(false);
            $table->boolean('belongs_to_interbank')->default(false);
            $table->boolean('dealable_fx_rate')->default(false);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });

        Schema::table('currency_pairs', function (Blueprint $table) {
            $table->foreign('counter_currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_pairs');
    }
}

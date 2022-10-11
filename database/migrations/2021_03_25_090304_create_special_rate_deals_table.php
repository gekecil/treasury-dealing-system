<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialRateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_rate_deals', function (Blueprint $table) {
            $table->foreignId('sales_deal_id')->primary()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->boolean('confirmed')->default(false);
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
        Schema::dropIfExists('special_rate_deals');
    }
}

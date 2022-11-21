<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSismontavarOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sismontavar_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('bank_id', 3);
            $table->string('secret_id', 64);
            $table->string('secret_key', 64);
            $table->string('username', 30);
            $table->decimal('threshold', 16, 2);
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
        Schema::dropIfExists('sismontavar_options');
    }
}

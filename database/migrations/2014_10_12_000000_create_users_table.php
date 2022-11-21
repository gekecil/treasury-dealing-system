<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 64)->unique();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('branch_code', 16)->nullable();
            $table->string('first_name', 16)->nullable();
            $table->string('last_name', 32)->nullable();
            $table->string('nik', 16)->nullable();
            $table->timestamps(0);
            $table->date('expires_at')->nullable();
            $table->softDeletes('deleted_at', 0);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

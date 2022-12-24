<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('userid')->unique();
            $table->string('chatid')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username');
            $table->string('language_code');
            $table->string('status');
            $table->string('firstcity')->nullable();
            $table->string('secondcity')->nullable();
            $table->string('date')->nullable();
            $table->string('weight')->nullable();
            $table->string('item')->nullable();
            $table->string('phone')->nullable();
            $table->string('price')->nullable();
            $table->string('isstart');
            $table->string('passport');
            $table->string('firstpassport')->nullable();
            $table->string('firstselfie')->nullable();
            $table->timestamps();
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
};

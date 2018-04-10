<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirtelMoneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airtel_moneys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number');
            $table->string('password');
            $table->integer('balance');
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
        Schema::dropIfExists('airtel_moneys');
    }
}

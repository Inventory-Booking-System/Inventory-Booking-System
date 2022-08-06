<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("user_id")->references('id')->on('users');
            $table->integer('status_id');
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->string('title');
            $table->string('location_id')->references('id')->on('locations');
            $table->text('details');
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
        Schema::dropIfExists('setups');
    }
}

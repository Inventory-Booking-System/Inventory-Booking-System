<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('start_date_time');
            $table->integer('status_id')->default(0);
            $table->integer('location_id')->references('id')->on('locations');
            $table->integer('distribution_id')->references('id')->on('distibution_groups');
            $table->text('evidence');
            $table->text('details');
            $table->text('resolution')->nullable();
            $table->integer('created_by');
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
        Schema::dropIfExists('incidents');
    }
}

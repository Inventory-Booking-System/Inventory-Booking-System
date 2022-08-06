<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributionGroupUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribution_group_user', function (Blueprint $table) {
            $table->integer('distribution_group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('distribution_group_id')
                ->references('id')
                ->on('distribution_groups');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribution_group_user');
    }
}

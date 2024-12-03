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
        Schema::create('asset_group_loan', function (Blueprint $table) {
            $table->integer('loan_id')->unsigned();
            $table->foreignId('asset_group_id');
            $table->integer('quantity');
            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->onDelete('cascade');
            $table->foreign('asset_group_id')
                ->references('id')
                ->on('asset_groups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_group_loan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_loan', function (Blueprint $table) {
            $table->integer('loan_id')->unsigned();
            $table->integer('asset_id')->unsigned();
            $table->boolean('returned')->default(false);
            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->onDelete('cascade');
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
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
        Schema::dropIfExists('asset_loan');
    }
}

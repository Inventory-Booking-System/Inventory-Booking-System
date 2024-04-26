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
        Schema::table('users', function (Blueprint $table) {
            // Add a foreign key column to link secondary users to their primary user
            $table->unsignedInteger('booking_authoriser_user_id')->nullable();
            $table->foreign('booking_authoriser_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                // Drop the foreign key column if the migration is rolled back
                $table->dropForeign(['booking_authoriser_user_id']);
                $table->dropColumn('booking_authoriser_user_id');
            });
        });
    }
};

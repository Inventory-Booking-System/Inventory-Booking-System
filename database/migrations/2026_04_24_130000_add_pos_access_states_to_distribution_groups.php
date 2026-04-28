<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosAccessStatesToDistributionGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distribution_groups', function (Blueprint $table) {
            $table->tinyInteger('pos_staff_screen_access')->default(0)->after('name');
            $table->tinyInteger('pos_student_screen_access')->default(0)->after('pos_staff_screen_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distribution_groups', function (Blueprint $table) {
            $table->dropColumn(['pos_staff_screen_access', 'pos_student_screen_access']);
        });
    }
}
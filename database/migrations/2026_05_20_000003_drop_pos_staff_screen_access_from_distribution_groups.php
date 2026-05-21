<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_groups', function (Blueprint $table) {
            $table->dropColumn('pos_staff_screen_access');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_groups', function (Blueprint $table) {
            $table->tinyInteger('pos_staff_screen_access')->default(0)->after('name');
        });
    }
};

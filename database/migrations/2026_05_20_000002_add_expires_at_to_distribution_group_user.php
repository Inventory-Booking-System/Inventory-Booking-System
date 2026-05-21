<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_group_user', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('distribution_group_user', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'created_at', 'updated_at']);
        });
    }
};

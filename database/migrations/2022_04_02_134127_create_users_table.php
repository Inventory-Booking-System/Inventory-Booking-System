<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('forename');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('role_id')->default(0)->references('role_id')->on('roles');
            $table->boolean('has_account')->default(0);
            $table->boolean('password_set')->default(0);
            $table->boolean('archived')->default(0);
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}

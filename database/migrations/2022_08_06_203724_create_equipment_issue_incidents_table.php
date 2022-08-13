<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentIssueIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_issue_incident', function (Blueprint $table) {
            $table->integer('incident_id')->unsigned();
            $table->integer('equipment_issue_id')->unsigned();
            $table->integer('quantity')->default(1);
            $table->foreign('incident_id')
                ->references('id')
                ->on('incidents')
                ->onDelete('cascade');
            $table->foreign('equipment_issue_id')
                ->references('id')
                ->on('equipment_issues')
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
        Schema::dropIfExists('equipment_issue_incident');
    }
}

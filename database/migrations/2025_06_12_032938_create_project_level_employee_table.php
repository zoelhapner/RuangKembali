<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('project_level_employee', function (Blueprint $table) {
        $table->id('project_level_id');
        $table->uuid('employee_id');

        $table->foreign('project_level_id')
              ->references('id')->on('project_levels')
              ->onDelete('cascade');

        $table->foreign('employee_id')
              ->references('id')->on('employees')
              ->onDelete('cascade');

        $table->primary(['project_level_id', 'employee_id']);
    });
}

public function down()
{
    Schema::dropIfExists('project_level_employee');
}

};

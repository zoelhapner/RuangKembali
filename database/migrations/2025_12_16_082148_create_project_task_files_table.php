<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_task_files', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('project_task_id');
            $table->string('file_path');
            $table->string('file_name')->nullable();

            $table->uuid('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_files');
    }
};

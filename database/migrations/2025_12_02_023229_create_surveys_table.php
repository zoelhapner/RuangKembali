<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id')->index();
            $table->uuid('created_by')->nullable(); // user yang mengisi (employee)
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->date('survey_date')->nullable();
            $table->time('survey_time')->nullable();
            $table->text('site_area')->nullable();
            $table->text('building_area')->nullable(); // ukuran tanah/bangunan
            $table->text('notes')->nullable(); // optional single field
            // signature file paths
            $table->boolean('consultant_signed')->default(false);
            $table->boolean('client_signed')->default(false);
            $table->json('survey_result')->nullable();
            $table->string('documentation')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::create('survey_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('survey_id')->index();
            $table->unsignedSmallInteger('order_no')->nullable();
            $table->text('description');
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->foreign('survey_id')->references('id')->on('surveys')->cascadeOnDelete();
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('survey_items');
    }
};

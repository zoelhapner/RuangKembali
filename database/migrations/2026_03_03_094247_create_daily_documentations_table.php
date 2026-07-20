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
        Schema::create('daily_documentations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_report_id')
                ->constrained('build_daily_reports')
                ->onDelete('cascade');

            $table->string('category'); 
            // tenaga / pekerjaan / material

            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_documentations');
    }
};

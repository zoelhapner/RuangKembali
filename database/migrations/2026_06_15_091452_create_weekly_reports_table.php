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
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('minggu');

            $table->longText('catatan')->nullable();
            $table->longText('capaian')->nullable();
            $table->longText('kendala')->nullable();
            $table->longText('rencana')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'minggu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};

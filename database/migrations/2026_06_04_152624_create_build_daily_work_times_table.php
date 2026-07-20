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
        Schema::create('build_daily_work_times', function (Blueprint $table) {
            $table->id();

            $table->foreignId('build_daily_report_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            $table->decimal('total_jam', 8, 2)->default(0);

            $table->string('cuaca')->nullable();

            $table->string('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_daily_work_times');
    }
};

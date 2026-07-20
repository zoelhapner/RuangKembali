<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_daily_workers', function (Blueprint $t) {
            $t->id();

            $t->foreignId('daily_report_id')
              ->constrained('build_daily_reports')
              ->cascadeOnDelete();

            $t->string('keahlian');
            $t->integer('jumlah')->default(0);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_daily_workers');
    }
};

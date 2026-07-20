<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_daily_materials', function (Blueprint $t) {
            $t->id();

            $t->foreignId('daily_report_id')
              ->constrained('build_daily_reports')
              ->cascadeOnDelete();

            $t->string('nama_bahan');
            $t->decimal('qty', 14, 2)->nullable();
            $t->string('satuan', 50)->nullable();

            $t->enum('status', ['diterima','ditolak'])
              ->default('diterima');

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_daily_materials');
    }
};

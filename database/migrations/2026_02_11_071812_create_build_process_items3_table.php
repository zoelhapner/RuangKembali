<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('build_process_items', function (Blueprint $t) {
            $t->foreignId('job_category_id')
            ->nullable()
            ->constrained('job_categories')
            ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Schema::dropIfExists('build_process_items');
    }
};

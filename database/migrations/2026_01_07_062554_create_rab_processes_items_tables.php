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
        Schema::create('rab_process_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('rab_process_id')
        ->constrained('rab_process')
        ->cascadeOnDelete();

    $table->foreignId('job_category_id')
        ->constrained('job_categories')
        ->cascadeOnDelete();

    $table->string('job_name');
    $table->string('satuan');

    $table->decimal('volume', 15, 8);
    $table->decimal('price', 15, 2);
    $table->decimal('total', 15, 2);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_process_items');
    }
};

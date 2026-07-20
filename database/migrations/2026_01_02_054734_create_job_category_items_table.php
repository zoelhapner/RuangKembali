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
        Schema::create('job_category_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('job_category_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('labor_cost_id')
          ->nullable()
          ->constrained('labor_costs')
          ->nullOnDelete();

    $table->foreignId('equipment_cost_id')
          ->nullable()
          ->constrained('equipment_costs')
          ->nullOnDelete();

    $table->uuid('product_id')->nullable();

    $table->string('category'); // product|labor|equipment
    $table->string('name');
    $table->string('code');
    $table->string('unit');
    $table->decimal('coefisien', 10, 4);

    $table->decimal('base_unit_price', 15, 2);
    $table->decimal('total_price', 15, 2);


    $table->timestamps();

    $table->foreign('product_id')
          ->references('id')
          ->on('products')
          ->nullOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_category_items');
    }
};

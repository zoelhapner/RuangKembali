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
        Schema::create('rab_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('price_meter')->default(0);
        });

                Schema::create('rab_packages_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rab_package_id')->references('id')->on('rab_packages')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->boolean('is_optional')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_packages');
        Schema::dropIfExists('rab_packages_items');
    }
};

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
        Schema::create('product_supplier', function (Blueprint $table) {

            $table->uuid('product_id');
            $table->uuid('supplier_id');

            $table->decimal('buying_prices', 15, 2)->nullable();
            $table->decimal('selling_prices', 15, 2)->nullable();
            $table->decimal('special_prices', 15, 2)->nullable();
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->string('stock')->nullable();

            $table->timestamps();

            // Foreign keys (optional kalau pakai UUID tanpa constraint)
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};

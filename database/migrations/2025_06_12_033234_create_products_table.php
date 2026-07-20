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
    Schema::create('products', function (Blueprint $table) {
        $table->uuid('id')->primary();

        $table->string('sku_code')->unique();
        $table->string('name');
        $table->string('photo')->nullable();
        $table->text('description')->nullable();

        // Units
        $table->string('unit_1_name')->default('PCS');
        $table->integer('unit_1_value')->default(1);

        $table->string('unit_2_name')->nullable();
        $table->integer('unit_2_value')->nullable();

        $table->string('unit_3_name')->nullable();
        $table->integer('unit_3_value')->nullable();

        $table->string('unit_4_name')->nullable();
        $table->integer('unit_4_value')->nullable();

        // Relations
        $table->unsignedBigInteger('brand_id')->nullable();
        $table->foreign('brand_id')->references('id')->on('product_brands')->nullOnDelete();

        $table->unsignedBigInteger('category_id')->nullable();
        $table->foreign('category_id')->references('id')->on('product_categories')->nullOnDelete();

        $table->unsignedBigInteger('type_id')->nullable();
        $table->foreign('type_id')->references('id')->on('product_types')->nullOnDelete();
        // $table->unsignedBigInteger('color_id')->nullable();
        // $table->foreign('color_id')->references('id')->on('colors')->nullOnDelete();

        // Product attributes
        $table->string('volume')->nullable();
        $table->string('size')->nullable();

        $table->unsignedBigInteger('inventory_account_id')->nullable();
        $table->unsignedBigInteger('sales_account_id')->nullable();
        $table->unsignedBigInteger('hpp_account_id')->nullable();

        // Status (1=Available,2=Limited,3=Out of stock,4=Pre-order)
        $table->unsignedTinyInteger('status')->default(1);

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

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
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->uuid('design_package_id');
            $table->string('offer_number');
            $table->string('contact_name');
            $table->string('volume');
            $table->string('satuan');
            $table->decimal('price_meter');
            $table->date('offer_date');
            $table->decimal('total_price');
            $table->decimal('discount');
            $table->string('tax_rate');
            $table->decimal('total_tax');
            $table->string('shipping');
            $table->decimal('grand_total');
            $table->string('notes')->nullable();
            $table->string('contract_number')->nullable();
            $table->date('contract_date')->nullable();
            $table->uuid('created_by')->nullable();       
            $table->enum('status', ['draft', 'approved', 'rejected'])
                ->default('draft');
            $table->text('reject_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('approved_by')->nullable();     
            
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('design_package_id')->references('id')->on('design_packages')->cascadeOnDelete();
        });

        Schema::create('offer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('offer_id');
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->boolean('is_optional')->default(false);           
            
            $table->foreign('offer_id')->references('id')->on('offers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
        Schema::dropIfExists('offer_items');
    }
};

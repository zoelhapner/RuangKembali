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
        Schema::create('accounting_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('license_id');
            // $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade');
            
            $table->string('account_code', 20)->unique();
            $table->string('account_name');
            $table->string('account_type');
            $table->boolean('is_parent')->default(false);

            $table->uuid('parent_id')->nullable();
    
            $table->string('balance_type');
            $table->decimal('initial_balance', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::table('accounting_accounts', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('accounting_accounts')->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_accounts');
    }
};

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
        Schema::create('accounting_closing_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('license_id');
            // $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade');
            $table->uuid('account_id');
            $table->foreign('account_id')->references('id')->on('accounting_accounts')->onDelete('cascade');
            $table->uuid('period_id');
            $table->foreign('period_id')->references('id')->on('accounting_periods')->onDelete('cascade');
            $table->decimal('closing_balance', 15, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_closing_balances');
    }
};

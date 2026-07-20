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
        Schema::create('accounting_journal_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id');
            $table->uuid('account_id');
            $table->float('debit')->default(0);
            $table->float('credit')->default(0);
            $table->text('description')->nullable();

            $table->foreign('journal_id')->references('id')->on('accounting_journals')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounting_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_details');
    }
};

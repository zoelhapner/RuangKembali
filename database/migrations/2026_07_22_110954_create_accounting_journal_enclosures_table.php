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
        Schema::create('accounting_journal_enclosures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_id')
                ->constrained('accounting_journals')
                ->cascadeOnDelete();

            $table->string('file_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_enclosures');
    }
};

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
        Schema::create('invoice_builds', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            // Relasi
            $table->foreignUuid('project_id')
                ->constrained()
                ->cascadeOnDelete();

            // Invoice Identity
            $table->string('invoice_number')->unique();
            $table->date('invoice_date')->nullable();
            $table->string('invoice_type')->default('build');

            // Termin & Progress
            $table->unsignedTinyInteger('termin');
            $table->unsignedTinyInteger('progress_start')->default(0);
            $table->unsignedTinyInteger('progress_end');
            $table->unsignedTinyInteger('payment_percentage');

            // Keuangan
            $table->decimal('amount', 15, 2);

            // Status
            $table->string('status')->default('draft');
            // draft | downloaded | approved | rejected | paid

            // Approval
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->string('approve_by_name')->nullable();
            $table->string('approved_ip')->nullable();
            $table->string('approval_token')->nullable();

            // Reject
            $table->timestamp('rejected_at')->nullable();
            $table->uuid('rejected_by')->nullable();
            $table->text('reject_note')->nullable();

            // Tracking
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Catatan
            $table->text('note')->nullable();

            $table->timestamps();

            // Proteksi: 1 termin per project
            $table->unique(['project_id', 'termin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_builds');
    }
};

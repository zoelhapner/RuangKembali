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

        $table->string('account_code', 20)->unique();
        $table->string('account_name');

        $table->string('category');
        $table->boolean('is_parent')->default(false);

        $table->uuid('parent_id')->nullable();

        $table->string('sub_category');
        $table->decimal('initial_balance', 15, 2)->nullable();

        $table->boolean('is_active')->default(true);

        $table->text('person_type')->nullable();

        $table->uuid('license_id')->nullable();
    });

    Schema::table('accounting_accounts', function (Blueprint $table) {
        $table->foreign('parent_id')
            ->references('id')
            ->on('accounting_accounts')
            ->nullOnDelete();

        // Di Railway saat ini BELUM ADA foreign key untuk license_id,
        // jadi jangan ditambahkan agar strukturnya sama persis.
        // $table->foreign('license_id')
        //     ->references('id')
        //     ->on('licenses')
        //     ->cascadeOnDelete();
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

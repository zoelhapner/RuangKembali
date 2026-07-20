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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('nik');
            $table->unsignedTinyInteger('marital_status')->nullable();;
            $table->string('position')->nullable();
            $table->string('employment_status');
            $table->string('start_date', 10)->nullable();;
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowance', 15, 2)->nullable();
            $table->decimal('deduction', 15, 2)->nullable();
            $table->decimal('bonus', 15, 2)->nullable();
            $table->decimal('thr', 15, 2)->nullable();
            $table->string('contract_letter_file');
            $table->string('training_certificate')->nullable();
            $table->string('photo')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

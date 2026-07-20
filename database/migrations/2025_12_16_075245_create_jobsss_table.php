<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        Schema::create('job_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('job_categories')
                ->cascadeOnDelete();

            $table->string('code')->index();      // A.2.2.1.3
            $table->string('name');               // Jenis pekerjaan
            $table->string('unit', 50)->nullable(); // m2, m3, buah
            $table->decimal('price', 15, 2);      // harga satuan

            $table->timestamps();

            $table->unique(['category_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_prices');
    }
};
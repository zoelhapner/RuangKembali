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
        Schema::table('build_process_items', function (Blueprint $table) {

            // penanda pekerjaan tambahan
            $table->boolean('is_tambahan')
                ->default(false)
                ->after('bobot_percent');

            // relasi ke item utama
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->after('is_tambahan');

            // sumber perubahan (opsional tapi berguna)
            $table->string('sumber', 50)
                ->nullable()
                ->after('parent_id');

            // foreign key self reference
            $table->foreign('parent_id')
                ->references('id')
                ->on('build_process_items')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_process_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'is_tambahan',
                'parent_id',
                'sumber'
            ]);
        });
    }
};

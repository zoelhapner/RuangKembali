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
        Schema::table('model_has_roles', function (Blueprint $table) {
            // Hapus FK & index lama kalau ada
            $table->dropIndex(['model_id', 'model_type']); // morph index
            $table->dropColumn('model_id');

            // Tambah ulang sebagai UUID
            $table->uuid('model_id')->index();
        });

          Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex(['model_id', 'model_type']);
            $table->dropColumn('model_id');
            $table->uuid('model_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropColumn('model_id');
            $table->unsignedBigInteger('model_id')->index();
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropColumn('model_id');
            $table->unsignedBigInteger('model_id')->index();
        });
    }
};

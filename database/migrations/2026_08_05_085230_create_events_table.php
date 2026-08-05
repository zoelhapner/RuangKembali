<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Jika sistem multi tenant
            $table->uuid('license_id')->nullable()->index();

            // Informasi utama
            $table->string('event_code', 30)->unique();
            $table->string('name');

            // Kategori
            $table->foreignUuid('event_category_id')->nullable()->constrained('event_categories')->nullOnDelete();

            // Jenis
            $table->enum('event_type', [
                'free',
                'paid'
            ])->default('free');

            // Audience
            $table->enum('audience_type', [
                'public',
                'gender',
                'age'
            ])->default('public');

            // Jadwal
            $table->date('registration_open')->nullable();
            $table->date('registration_close')->nullable();

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // Lokasi
            $table->string('location')->nullable();

            // HTM
            $table->decimal('price', 15, 2)->default(0);

            // Kuota
            $table->integer('quota')->nullable();

            // Poster
            $table->string('poster')->nullable();

            // Thumbnail
            $table->string('thumbnail')->nullable();

            // Deskripsi
            $table->longText('description')->nullable();

            // Status
            $table->enum('status', [
                'coming_soon',
                'registration_open',
                'sold_out',
                'ongoing',
                'finished',
                'cancelled'
            ])->default('coming_soon');

            // Publish
            $table->boolean('is_published')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'status',
                'start_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
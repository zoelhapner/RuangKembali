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
        Schema::create('licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('license_id');
            $table->string('license_type', 2);
            $table->string('name');
            $table->string('email');
            $table->longText('address');
            $table->unsignedBigInteger('province_id'); 
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->unsignedBigInteger('sub_district_id')->nullable();
            $table->foreign('sub_district_id')->references('id')->on('sub_districts')->onDelete('cascade');
            $table->unsignedBigInteger('postal_code_id')->nullable();
            $table->foreign('postal_code_id')->references('id')->on('postal_codes')->onDelete('cascade');
            $table->string('phone');
            $table->string('join_date', 10);
            $table->string('expired_date', 10);
            $table->string('contract_agreement_number');
            $table->string('contract_document');
            $table->string('document_form');
            $table->string('status', 10);
            $table->unsignedTinyInteger('building_type')->nullable();
            $table->unsignedTinyInteger('building_status')->nullable();
            $table->string('building_rent_expired_date', 10)->nullable();
            $table->decimal('building_area', 8, 2)->nullable();
            $table->unsignedTinyInteger('building_condition')->nullable();
            $table->boolean('building_has_ac')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook_page')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('youtube')->nullable();
            $table->string('google_maps')->nullable();
            $table->string('landing_page_student_registration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};

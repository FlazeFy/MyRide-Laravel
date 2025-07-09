<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vehicle_name', 75);
            $table->string('vehicle_merk', 36);
            $table->string('vehicle_type', 36);
            $table->integer('vehicle_price')->length(13);
            $table->string('vehicle_desc', 500)->nullable();
            $table->integer('vehicle_distance')->length(7);
            $table->string('vehicle_category', 36);
            $table->string('vehicle_status', 36);
            $table->integer('vehicle_year_made')->length(4);
            $table->string('vehicle_plate_number', 14);
            $table->string('vehicle_fuel_status', 36);
            $table->integer('vehicle_fuel_capacity')->length(3);
            $table->string('vehicle_default_fuel', 36);
            $table->string('vehicle_color', 36);
            $table->string('vehicle_transmission', 14);
            $table->string('vehicle_img_url', 500)->nullable();
            $table->json('vehicle_other_img_url')->nullable();
            $table->integer('vehicle_capacity')->length(2);
            $table->json('vehicle_document')->nullable();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', 36);
            $table->dateTime('updated_at', $precision = 0)->nullable();
            $table->dateTime('deleted_at', $precision = 0)->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_merk')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_type')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_category')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_status')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_default_fuel')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_transmission')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_fuel_status')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
            $table->foreign('vehicle_color')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle');
    }
};

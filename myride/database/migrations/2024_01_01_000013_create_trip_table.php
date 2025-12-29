<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->uuid('driver_id')->nullable();
            $table->string('trip_desc', 500);
            $table->string('trip_category', 36);
            $table->string('trip_person', 255)->nullable();
            $table->string('trip_origin_name', 75);
            $table->string('trip_origin_coordinate', 144)->nullable();
            $table->string('trip_destination_name', 75);
            $table->string('trip_destination_coordinate', 144)->nullable();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->uuid('created_by');
            $table->dateTime('updated_at', $precision = 0)->nullable();
            $table->dateTime('deleted_at', $precision = 0)->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('driver')->onDelete('cascade');
            $table->foreign('trip_category')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_vehicle_relation', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->uuid('driver_id');
            $table->string('relation_note', 255)->nullable();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->uuid('created_by');

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('driver')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_vehicle_relation');
    }
};

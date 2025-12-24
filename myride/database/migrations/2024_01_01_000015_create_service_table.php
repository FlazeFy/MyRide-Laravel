<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->longText('service_note')->nullable();
            $table->string('service_category', 36);
            $table->string('service_price_total', 9);
            $table->string('service_location', 255);
            $table->string('notes', 1000)->nullable();
            $table->uuid('created_by');

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('remind_at')->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};

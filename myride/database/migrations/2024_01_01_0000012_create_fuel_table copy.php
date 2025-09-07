<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vehicle_id', 36);
            $table->integer('fuel_volume')->length(3);
            $table->integer('fuel_price_total')->length(9);
            $table->string('fuel_brand', 255);
            $table->string('fuel_type', 75)->nullable();
            $table->integer('fuel_ron', 144)->length(2)->nullable();
            $table->longText('fuel_bill')->nullable();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', 36);

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel');
    }
};

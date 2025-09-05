<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clean', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vehicle_id', 36);
            $table->string('clean_desc', 500)->nullable();
            $table->string('clean_by', 75);
            $table->string('clean_tools', 500)->nullable();
            $table->boolean('is_clean_body');
            $table->boolean('is_clean_window');
            $table->boolean('is_clean_dashboard');
            $table->boolean('is_clean_tires');
            $table->boolean('is_clean_trash');
            $table->boolean('is_clean_engine');
            $table->boolean('is_clean_seat');
            $table->boolean('is_clean_carpet');
            $table->boolean('is_clean_pillows');
            $table->boolean('is_fill_window_cleaning_water');
            $table->boolean('is_clean_hollow');
            $table->string('clean_address', 255)->nullable();
            $table->dateTime('clean_start_time', $precision = 0);
            $table->dateTime('clean_end_time', $precision = 0)->nullable();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', 36);
            $table->dateTime('updated_at', $precision = 0)->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
            $table->foreign('clean_by')->references('dictionary_name')->on('dictionary')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clean');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wash', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->string('wash_desc', 500)->nullable();
            $table->string('wash_by', 75);
            $table->string('wash_tools', 500)->nullable();
            $table->boolean('is_wash_body');
            $table->boolean('is_wash_window');
            $table->boolean('is_wash_dashboard');
            $table->boolean('is_wash_tires');
            $table->boolean('is_wash_trash');
            $table->boolean('is_wash_engine');
            $table->boolean('is_wash_seat');
            $table->boolean('is_wash_carpet');
            $table->boolean('is_wash_pillows');
            $table->string('wash_address', 255)->nullable();
            $table->dateTime('wash_start_time')->nullable();
            $table->dateTime('wash_end_time')->nullable();
            $table->boolean('is_fill_window_washing_water');
            $table->boolean('is_wash_hollow');

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->uuid('created_by');
            $table->dateTime('updated_at', $precision = 0)->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wash');
    }
};

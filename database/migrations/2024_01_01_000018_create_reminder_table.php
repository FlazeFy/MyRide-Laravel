<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->string('reminder_title', 75);
            $table->string('reminder_context', 36);
            $table->string('reminder_body', 255);
            $table->json('reminder_attachment')->nullable();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->dateTime('remind_at', $precision = 0);
            $table->uuid('created_by');

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder');
    }
};

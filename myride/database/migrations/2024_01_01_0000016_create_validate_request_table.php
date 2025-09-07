<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validate_request', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request_type', 144);
            $table->string('request_context', 255)->nullable();
            $table->boolean('is_show');
            $table->string('created_by', 36);           

            // Props
            $table->dateTime('created_at', $precision = 0);

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validate_request');
    }
};

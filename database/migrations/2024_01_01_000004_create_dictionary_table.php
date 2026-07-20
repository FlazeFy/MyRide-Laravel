<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dictionary', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dictionary_type', 36);
            $table->string('dictionary_name', 75)->unique();

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->uuid('created_by')->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictionary');
    }
};

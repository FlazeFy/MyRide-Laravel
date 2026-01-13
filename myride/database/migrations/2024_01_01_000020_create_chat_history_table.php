<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('question', 255);
            $table->string('answer', 500)->nullable();
            $table->string('intent', 144)->nullable();
            $table->boolean('is_success');

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->uuid('created_by');

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_history');
    }
};

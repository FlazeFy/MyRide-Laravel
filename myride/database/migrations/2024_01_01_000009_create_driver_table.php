<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 36);
            $table->string('fullname', 50);
            $table->string('password', 255);
            $table->string('email', 255);
            $table->string('telegram_user_id', 36)->nullable();
            $table->boolean('telegram_is_valid');
            $table->string('phone', 16);
            $table->string('notes', 500)->nullable();

            // Props
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver');
    }
};

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
            $table->longText('service_context')->nullable();
            $table->string('service_category', 36);
            $table->boolean('service_is_payment');
            $table->string('service_payment_amount', 9);
            $table->string('service_location', 255);
            $table->string('notes', 1000)->nullable();
            $table->string('created_by', 36);

            // Props
            $table->dateTime('created_at', $precision = 0);
            $table->dateTime('updated_at')->nullable();

            // References
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};

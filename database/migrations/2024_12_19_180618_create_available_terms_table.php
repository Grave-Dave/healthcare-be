<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('available_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['available', 'booked', 'canceled'])->default('available');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_terms');
    }
};

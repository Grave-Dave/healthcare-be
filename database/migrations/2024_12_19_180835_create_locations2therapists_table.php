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
        Schema::create('locations2therapists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade')->index();
            $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations2therapists');
    }
};

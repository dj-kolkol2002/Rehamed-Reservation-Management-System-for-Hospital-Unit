<?php
// database/migrations/2024_01_01_000000_create_appointments_table.php

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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['fizjoterapia', 'konsultacja', 'masaz', 'neurorehabilitacja', 'kontrola']);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->string('color', 7)->nullable(); // Hex color code
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indeksy dla wydajności
            $table->index(['doctor_id', 'start_time']);
            $table->index(['patient_id', 'start_time']);
            $table->index(['status', 'start_time']);
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

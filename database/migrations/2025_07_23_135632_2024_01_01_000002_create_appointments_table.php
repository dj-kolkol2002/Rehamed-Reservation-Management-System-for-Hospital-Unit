<?php
// database/migrations/2024_01_01_000002_create_appointments_table.php

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

            // Relacje z użytkownikami
            $table->foreignId('patient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();

            // Podstawowe informacje o wizycie
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('consultation'); // consultation, therapy, massage, exercise, other

            // Czas wizyty
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Status i szczegóły
            $table->string('status')->default('scheduled'); // scheduled, confirmed, in_progress, completed, cancelled, rescheduled
            $table->text('notes')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('room')->nullable();

            // Powiadomienia (JSON)
            $table->json('notifications')->nullable();

            // Anulowanie
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            // Indeksy dla lepszej wydajności
            $table->index(['doctor_id', 'start_time']);
            $table->index(['patient_id', 'start_time']);
            $table->index(['status', 'start_time']);
            $table->index('start_time');
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

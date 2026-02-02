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
        Schema::create('slot_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('date'); // Data konkretnego dnia
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_patients')->default(1); // Limit pacjentów na slot
            $table->integer('current_bookings')->default(0); // Aktualna liczba rezerwacji
            $table->boolean('is_available')->default(true); // Czy slot jest dostępny dla pacjentów
            $table->enum('visibility', ['public', 'restricted', 'hidden'])->default('public');
            // public - widoczny dla wszystkich
            // restricted - tylko dla wybranych pacjentów (np. kontynuacja leczenia)
            // hidden - niewidoczny, tylko doktor może przypisać
            $table->json('allowed_patient_ids')->nullable(); // Lista ID pacjentów dla restricted
            $table->json('metadata')->nullable(); // Dodatkowe dane
            $table->timestamps();

            // Indeksy
            $table->index(['doctor_id', 'date']);
            $table->index(['doctor_id', 'is_available']);
            $table->unique(['doctor_id', 'date', 'start_time']); // Unikalne sloty
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_availability');
    }
};

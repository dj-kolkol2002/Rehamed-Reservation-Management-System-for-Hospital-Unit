<?php
// database/migrations/2025_12_14_000001_create_doctor_schedules_table.php

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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');

            // Dzień tygodnia (0 = niedziela, 1 = poniedziałek, ..., 6 = sobota)
            $table->integer('day_of_week');

            // Godziny pracy
            $table->time('start_time');      // np. 08:00
            $table->time('end_time');        // np. 20:00

            // Czas przerwy (np. na lunch)
            $table->time('break_start')->nullable();  // np. 12:00
            $table->time('break_end')->nullable();    // np. 13:00

            // Długość slotu wizyt (w minutach, np. 30, 60)
            $table->integer('appointment_duration')->default(60);

            // Czy ta godzina jest aktywna
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indeksy dla wydajności
            $table->unique(['doctor_id', 'day_of_week']);
            $table->index(['doctor_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};

<?php
// database/migrations/2026_01_23_000001_create_clinic_hours_table.php

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
        Schema::create('clinic_hours', function (Blueprint $table) {
            $table->id();

            // Dzień tygodnia (0 = niedziela, 1 = poniedziałek, ..., 6 = sobota)
            $table->integer('day_of_week');

            // Godziny otwarcia kliniki
            $table->time('start_time');      // np. 07:00
            $table->time('end_time');        // np. 21:00

            // Czy klinika jest otwarta w ten dzień
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Jeden wpis na dzień tygodnia
            $table->unique('day_of_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_hours');
    }
};

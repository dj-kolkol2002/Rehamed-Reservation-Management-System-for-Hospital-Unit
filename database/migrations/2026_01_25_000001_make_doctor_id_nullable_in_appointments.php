<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pozwala na tworzenie wizyt bez przypisanego lekarza (nowy system rezerwacji)
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Usuń stary foreign key constraint
            $table->dropForeign(['doctor_id']);

            // Zmień kolumnę na nullable
            $table->foreignId('doctor_id')->nullable()->change();

            // Dodaj nowy foreign key z nullable
            $table->foreign('doctor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Usuń foreign key
            $table->dropForeign(['doctor_id']);

            // Zmień z powrotem na NOT NULL (wymaga że wszystkie wizyty mają doctor_id)
            $table->foreignId('doctor_id')->nullable(false)->change();

            // Dodaj oryginalny foreign key
            $table->foreign('doctor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};

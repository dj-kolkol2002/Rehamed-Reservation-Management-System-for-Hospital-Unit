<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'patient_name')) {
                $table->string('patient_name')->nullable()->after('patient_id');
            }
            if (!Schema::hasColumn('appointments', 'doctor_name')) {
                $table->string('doctor_name')->nullable()->after('doctor_id');
            }
        });

        // Wypełnienie istniejących rekordów danymi z relacji
        DB::statement('
            UPDATE appointments
            SET patient_name = (SELECT CONCAT(firstname, " ", lastname) FROM users WHERE users.id = appointments.patient_id)
            WHERE patient_id IS NOT NULL
        ');

        DB::statement('
            UPDATE appointments
            SET doctor_name = (SELECT CONCAT(firstname, " ", lastname) FROM users WHERE users.id = appointments.doctor_id)
            WHERE doctor_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['patient_name', 'doctor_name']);
        });
    }
};

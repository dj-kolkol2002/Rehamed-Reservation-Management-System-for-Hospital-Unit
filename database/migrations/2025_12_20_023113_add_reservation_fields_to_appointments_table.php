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
        Schema::table('appointments', function (Blueprint $table) {
            // Typ rezerwacji
            $table->enum('reservation_type', ['online', 'phone', 'in_person', 'doctor_created'])
                ->default('doctor_created')
                ->after('status');

            // Status rezerwacji (dla wniosków pacjentów)
            $table->enum('reservation_status', ['pending', 'confirmed', 'rejected', 'auto_confirmed'])
                ->nullable()
                ->after('reservation_type');

            // Priorytet wizyty
            $table->enum('priority', ['normal', 'urgent', 'emergency'])
                ->default('normal')
                ->after('reservation_status');

            // Czy pacjent może odwołać
            $table->boolean('patient_can_cancel')->default(true)->after('priority');

            // Data potwierdzenia rezerwacji przez lekarza
            $table->timestamp('confirmed_at')->nullable()->after('patient_can_cancel');

            // Data odrzucenia rezerwacji
            $table->timestamp('rejected_at')->nullable()->after('confirmed_at');

            // Powód odrzucenia
            $table->text('rejection_reason')->nullable()->after('rejected_at');

            // Indeksy
            $table->index('reservation_status');
            $table->index(['doctor_id', 'reservation_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'reservation_status']);
            $table->dropIndex(['reservation_status']);
            $table->dropColumn([
                'reservation_type',
                'reservation_status',
                'priority',
                'patient_can_cancel',
                'confirmed_at',
                'rejected_at',
                'rejection_reason'
            ]);
        });
    }
};

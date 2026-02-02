<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', [
                'consultation',
                'therapy',
                'massage',
                'exercise',
                'other'
            ])->default('consultation');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'rescheduled'
            ])->default('scheduled');
            $table->text('notes')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('room')->nullable();
            $table->json('notifications')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Indeksy dla lepszej wydajności
            $table->index(['patient_id', 'start_time']);
            $table->index(['doctor_id', 'start_time']);
            $table->index(['start_time', 'end_time']);
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

<?php
// database/migrations/2024_01_25_create_medical_documents_table.php

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
        Schema::create('medical_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('general'); // general, diagnosis, treatment, examination, prescription
            $table->text('content');
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable(); // Ścieżka do załączonego pliku
            $table->string('file_name')->nullable(); // Oryginalna nazwa pliku
            $table->enum('status', ['draft', 'completed', 'archived'])->default('draft');
            $table->date('document_date')->default(now());
            $table->json('metadata')->nullable(); // Dodatkowe metadane (objawy, leki, itp.)
            $table->boolean('is_private')->default(false); // Czy dokument jest prywatny (widoczny tylko dla doktora)
            $table->timestamps();

            // Indeksy
            $table->index(['patient_id', 'created_at']);
            $table->index(['doctor_id', 'created_at']);
            $table->index(['type', 'status']);
            $table->index('document_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_documents');
    }
};

<?php
// database/migrations/2024_01_01_000000_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // appointment_created, document_created, message_received, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // dodatkowe dane
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('related_id')->nullable(); // ID powiązanego obiektu
            $table->string('related_type')->nullable(); // klasa powiązanego obiektu
            $table->string('icon')->nullable(); // niestandardowa ikona
            $table->string('action_url')->nullable(); // URL do akcji
            $table->timestamps();

            // Indeksy
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index(['type']);
            $table->index(['related_id', 'related_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

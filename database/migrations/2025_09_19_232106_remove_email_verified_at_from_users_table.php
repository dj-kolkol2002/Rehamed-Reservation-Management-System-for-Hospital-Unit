<?php
// database/migrations/YYYY_MM_DD_HHMMSS_remove_email_verified_at_from_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            // Usuń kolumnę email_verified_at jeśli istnieje
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Przywróć kolumnę email_verified_at
            $table->timestamp('email_verified_at')->nullable();
        });
    }
};

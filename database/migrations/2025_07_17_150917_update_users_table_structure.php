<?php
// database/migrations/2024_07_17_000001_update_users_table_structure.php

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
        Schema::table('users', function (Blueprint $table) {
            // Sprawdź czy kolumny już istnieją, jeśli nie - dodaj je
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->after('id');
            }

            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->after('firstname');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'doctor', 'user'])->default('user')->after('password');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('address');
            }

            if (!Schema::hasColumn('users', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('users', 'medical_history')) {
                $table->json('medical_history')->nullable()->after('emergency_contact');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('medical_history');
            }
        });

        // Najpierw usuń istniejącą kolumnę gender jeśli istnieje z nieprawidłowym typem
        if (Schema::hasColumn('users', 'gender')) {
            // Sprawdź aktualny typ kolumny
            $columns = DB::select("SHOW COLUMNS FROM users LIKE 'gender'");
            if (!empty($columns)) {
                $column = $columns[0];
                // Jeśli to nie jest poprawny ENUM, usuń kolumnę
                if (!str_contains($column->Type, "enum('male','female','other')")) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropColumn('gender');
                    });
                }
            }
        }

        // Teraz dodaj kolumnę gender z prawidłowym typem
        if (!Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            });
        }

        // Dodaj indeksy jeśli nie istnieją
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['role', 'is_active'], 'users_role_is_active_index');
            });
        } catch (Exception $e) {
            // Indeks już istnieje
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['firstname', 'lastname'], 'users_firstname_lastname_index');
            });
        } catch (Exception $e) {
            // Indeks już istnieje
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Usuń dodane kolumny
            $columnsToRemove = [
                'firstname', 'lastname', 'role', 'phone', 'address',
                'date_of_birth', 'gender', 'emergency_contact',
                'medical_history', 'is_active'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

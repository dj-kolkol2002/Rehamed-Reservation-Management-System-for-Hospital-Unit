<?php
// database/migrations/[timestamp]_add_rehamed_columns_to_users_table.php

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
        // Sprawdź czy kolumna 'name' istnieje i zmień ją na firstname/lastname
        if (Schema::hasColumn('users', 'name') && !Schema::hasColumn('users', 'firstname')) {
            // Najpierw dodaj nowe kolumny
            Schema::table('users', function (Blueprint $table) {
                $table->string('firstname')->after('id');
                $table->string('lastname')->after('firstname');
            });

            // Przekopiuj dane z 'name' do firstname (jeśli są jakieś dane)
            DB::statement("UPDATE users SET firstname = SUBSTRING_INDEX(name, ' ', 1), lastname = SUBSTRING_INDEX(name, ' ', -1) WHERE name IS NOT NULL");

            // Usuń starą kolumnę name
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        } else {
            // Dodaj kolumny jeśli nie istnieją
            if (!Schema::hasColumn('users', 'firstname')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('firstname')->after('id');
                });
            }

            if (!Schema::hasColumn('users', 'lastname')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('lastname')->after('firstname');
                });
            }
        }

        // Dodaj pozostałe kolumny
        Schema::table('users', function (Blueprint $table) {
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

        // Usuń i dodaj ponownie kolumnę gender z prawidłowym typem
        if (Schema::hasColumn('users', 'gender')) {
            // Sprawdź typ kolumny gender
            $columns = DB::select("SHOW COLUMNS FROM users LIKE 'gender'");
            if (!empty($columns)) {
                $column = $columns[0];
                if (!str_contains($column->Type, "enum('male','female','other')")) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropColumn('gender');
                    });
                }
            }
        }

        if (!Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            });
        }

        // Dodaj indeksy
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

            // Przywróć kolumnę name jeśli była
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->after('id');
            }
        });
    }
};

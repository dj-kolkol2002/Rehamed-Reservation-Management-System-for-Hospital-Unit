<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Konwertuj wszystkie appointment'y z Warsaw time → UTC
        // Dane w DB są przechowywane jako datetime strings bez timezone info
        // Były tworzone w Warsaw time, więc musimy je przesunąć na UTC
        DB::statement("
            UPDATE appointments
            SET start_time = DATE_SUB(start_time, INTERVAL 1 HOUR)
            WHERE start_time IS NOT NULL
        ");

        DB::statement("
            UPDATE appointments
            SET end_time = DATE_SUB(end_time, INTERVAL 1 HOUR)
            WHERE end_time IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert: UTC → Warsaw time
        DB::statement("
            UPDATE appointments
            SET start_time = DATE_ADD(start_time, INTERVAL 1 HOUR)
            WHERE start_time IS NOT NULL
        ");

        DB::statement("
            UPDATE appointments
            SET end_time = DATE_ADD(end_time, INTERVAL 1 HOUR)
            WHERE end_time IS NOT NULL
        ");
    }
};

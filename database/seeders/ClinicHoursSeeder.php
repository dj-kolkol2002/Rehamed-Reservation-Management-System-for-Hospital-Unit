<?php
// database/seeders/ClinicHoursSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClinicHours;

class ClinicHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Domyślne godziny otwarcia kliniki
        // Poniedziałek-Piątek: 07:00-21:00
        // Sobota: 08:00-16:00
        // Niedziela: zamknięte
        $defaultHours = [
            // Poniedziałek
            [
                'day_of_week' => 1,
                'start_time' => '07:00',
                'end_time' => '21:00',
                'is_active' => true,
            ],
            // Wtorek
            [
                'day_of_week' => 2,
                'start_time' => '07:00',
                'end_time' => '21:00',
                'is_active' => true,
            ],
            // Środa
            [
                'day_of_week' => 3,
                'start_time' => '07:00',
                'end_time' => '21:00',
                'is_active' => true,
            ],
            // Czwartek
            [
                'day_of_week' => 4,
                'start_time' => '07:00',
                'end_time' => '21:00',
                'is_active' => true,
            ],
            // Piątek
            [
                'day_of_week' => 5,
                'start_time' => '07:00',
                'end_time' => '21:00',
                'is_active' => true,
            ],
            // Sobota
            [
                'day_of_week' => 6,
                'start_time' => '08:00',
                'end_time' => '16:00',
                'is_active' => true,
            ],
            // Niedziela - zamknięte
            [
                'day_of_week' => 0,
                'start_time' => '00:00',
                'end_time' => '00:00',
                'is_active' => false,
            ],
        ];

        foreach ($defaultHours as $hours) {
            ClinicHours::updateOrCreate(
                ['day_of_week' => $hours['day_of_week']],
                $hours
            );
        }

        $this->command->info('ClinicHoursSeeder - Domyślne godziny pracy kliniki zostały utworzone!');
    }
}

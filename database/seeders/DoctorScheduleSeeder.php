<?php
// database/seeders/DoctorScheduleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DoctorSchedule;

class DoctorScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pobierz wszystkich lekarzy
        $doctors = User::where('role', 'doctor')->get();

        if ($doctors->isEmpty()) {
            $this->command->info('Brak lekarzy w bazie danych. Najpierw utwórz lekarzy.');
            return;
        }

        // Domyślny harmonogram: Poniedziałek-Piątek 8:00-20:00 z przerwą 12:00-13:00
        // Sobota 9:00-15:00, Niedziela zamknięte
        $defaultSchedules = [
            // Poniedziałek
            [
                'day_of_week' => 1,
                'start_time' => '08:00',
                'end_time' => '20:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'appointment_duration' => 60,
                'is_active' => true
            ],
            // Wtorek
            [
                'day_of_week' => 2,
                'start_time' => '08:00',
                'end_time' => '20:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'appointment_duration' => 60,
                'is_active' => true
            ],
            // Środa
            [
                'day_of_week' => 3,
                'start_time' => '08:00',
                'end_time' => '20:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'appointment_duration' => 60,
                'is_active' => true
            ],
            // Czwartek
            [
                'day_of_week' => 4,
                'start_time' => '08:00',
                'end_time' => '20:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'appointment_duration' => 60,
                'is_active' => true
            ],
            // Piątek
            [
                'day_of_week' => 5,
                'start_time' => '08:00',
                'end_time' => '20:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'appointment_duration' => 60,
                'is_active' => true
            ],
            // Sobota
            [
                'day_of_week' => 6,
                'start_time' => '09:00',
                'end_time' => '15:00',
                'break_start' => null,
                'break_end' => null,
                'appointment_duration' => 60,
                'is_active' => true
            ],
            // Niedziela - zamknięte
            [
                'day_of_week' => 0,
                'start_time' => '09:00',
                'end_time' => '09:00',
                'break_start' => null,
                'break_end' => null,
                'appointment_duration' => 60,
                'is_active' => false
            ]
        ];

        // Utwórz harmonogramy dla każdego lekarza
        foreach ($doctors as $doctor) {
            foreach ($defaultSchedules as $schedule) {
                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $schedule['day_of_week']
                    ],
                    $schedule
                );
            }

            $this->command->info("Harmonogram utworzony dla: {$doctor->firstname} {$doctor->lastname}");
        }

        $this->command->info('DoctorScheduleSeeder - Seeding completed!');
    }
}

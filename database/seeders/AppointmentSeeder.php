<?php
// database/seeders/AppointmentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pobierz doktorów i pacjentów
        $doctors = User::where('role', 'doctor')->get();
        $patients = User::where('role', 'user')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            $this->command->error('Brak doktorów lub pacjentów w bazie danych. Uruchom najpierw UserSeeder.');
            return;
        }

        $types = ['fizjoterapia', 'konsultacja', 'masaz', 'neurorehabilitacja', 'kontrola'];
        $statuses = ['scheduled', 'confirmed', 'completed'];

        // Generuj wizyty na najbliższe 4 tygodnie
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->addWeeks(4);

        $appointments = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Pomijaj weekendy
            if ($date->isWeekend()) {
                continue;
            }

            // Dla każdego doktora
            foreach ($doctors as $doctor) {
                // Losowa liczba wizyt dziennie (1-6)
                $appointmentsPerDay = rand(1, 6);

                for ($i = 0; $i < $appointmentsPerDay; $i++) {
                    // Losowa godzina między 8:00 a 17:00
                    $hour = rand(8, 16);
                    $minute = rand(0, 1) * 30; // 0 lub 30 minut

                    $startTime = $date->copy()->setTime($hour, $minute);
                    $endTime = $startTime->copy()->addHour(); // Wizyta trwa godzinę

                    // Sprawdź czy nie ma konfliktu
                    $conflict = collect($appointments)->first(function ($apt) use ($doctor, $startTime, $endTime) {
                        return $apt['doctor_id'] === $doctor->id &&
                               $startTime->between($apt['start_time'], $apt['end_time']) ||
                               $endTime->between($apt['start_time'], $apt['end_time']);
                    });

                    if ($conflict) {
                        continue; // Pomiń jeśli jest konflikt
                    }

                    $type = $types[array_rand($types)];
                    $patient = $patients->random();

                    // Status zależy od daty
                    $status = 'scheduled';
                    if ($startTime->isPast()) {
                        $status = 'completed';
                    } elseif ($startTime->diffInDays() <= 3) {
                        $status = 'confirmed';
                    }

                    $appointment = [
                        'title' => $this->generateTitle($type, $patient),
                        'description' => $this->generateDescription($type),
                        'type' => $type,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'doctor_id' => $doctor->id,
                        'patient_id' => $patient->id,
                        'status' => $status,
                        'notes' => $this->generateNotes($type),
                        'price' => $this->getPrice($type),
                        'room' => 'Gabinet ' . rand(1, 5),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $appointments[] = $appointment;
                }
            }
        }

        // Dodaj również kilka wizyt bez pacjenta (wolne terminy)
        foreach ($doctors as $doctor) {
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::now()->addDays(rand(1, 14));
                if ($date->isWeekend()) continue;

                $hour = rand(8, 16);
                $minute = rand(0, 1) * 30;
                $startTime = $date->copy()->setTime($hour, $minute);
                $endTime = $startTime->copy()->addHour();

                $appointments[] = [
                    'title' => 'Dostępny termin',
                    'description' => 'Wolny termin do rezerwacji',
                    'type' => 'konsultacja',
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'doctor_id' => $doctor->id,
                    'patient_id' => null,
                    'status' => 'scheduled',
                    'notes' => null,
                    'price' => null,
                    'room' => 'Gabinet ' . rand(1, 5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Wstaw wszystkie wizyty do bazy
        foreach (array_chunk($appointments, 50) as $chunk) {
            Appointment::insert($chunk);
        }

        $this->command->info('Utworzono ' . count($appointments) . ' wizyt.');
    }

    private function generateTitle($type, $patient)
    {
        $titles = [
            'fizjoterapia' => 'Fizjoterapia - ' . $patient->firstname . ' ' . $patient->lastname,
            'konsultacja' => 'Konsultacja - ' . $patient->firstname . ' ' . $patient->lastname,
            'masaz' => 'Masaż leczniczy - ' . $patient->firstname . ' ' . $patient->lastname,
            'neurorehabilitacja' => 'Neurorehabilitacja - ' . $patient->firstname . ' ' . $patient->lastname,
            'kontrola' => 'Wizyta kontrolna - ' . $patient->firstname . ' ' . $patient->lastname,
        ];

        return $titles[$type] ?? 'Wizyta - ' . $patient->firstname . ' ' . $patient->lastname;
    }

    private function generateDescription($type)
    {
        $descriptions = [
            'fizjoterapia' => 'Sesja fizjoterapii obejmująca ćwiczenia rehabilitacyjne i terapię manualną.',
            'konsultacja' => 'Konsultacja fizjoterapeutyczna - wywiad, badanie, plan terapii.',
            'masaz' => 'Masaż leczniczy tkanek miękkich w celu redukcji napięcia mięśniowego.',
            'neurorehabilitacja' => 'Rehabilitacja neurologiczna - terapia zaburzeń układu nerwowego.',
            'kontrola' => 'Wizyta kontrolna - ocena postępów w terapii i ewentualne modyfikacje.',
        ];

        return $descriptions[$type] ?? 'Wizyta fizjoterapeutyczna';
    }

    private function generateNotes($type)
    {
        $notes = [
            'fizjoterapia' => [
                'Pacjent zgłasza poprawę mobilności',
                'Kontynuacja ćwiczeń w domu',
                'Dobra współpraca podczas terapii',
                'Zalecenia dotyczące ergonomii pracy'
            ],
            'konsultacja' => [
                'Ustalono plan terapii',
                'Pacjent otrzymał zalecenia',
                'Konieczne badania dodatkowe',
                'Wyjaśniono przebieg terapii'
            ],
            'masaz' => [
                'Zmniejszenie napięcia mięśniowego',
                'Poprawa krążenia w okolicy',
                'Pacjent odczuwa ulgę',
                'Zalecenia pomiędzy sesjami'
            ],
            'neurorehabilitacja' => [
                'Postępy w koordynacji ruchowej',
                'Ćwiczenia równoważni',
                'Praca nad precyzją ruchów',
                'Wsparcie psychologiczne'
            ],
            'kontrola' => [
                'Ocena pozytywna',
                'Modyfikacja planu terapii',
                'Zalecenia na okres wakacyjny',
                'Ustalono kolejny termin'
            ]
        ];

        $typeNotes = $notes[$type] ?? ['Standardowa wizyta fizjoterapeutyczna'];
        return $typeNotes[array_rand($typeNotes)];
    }

    private function getPrice($type)
    {
        $prices = [
            'fizjoterapia' => 80.00,
            'konsultacja' => 100.00,
            'masaz' => 70.00,
            'neurorehabilitacja' => 120.00,
            'kontrola' => 60.00,
        ];

        return $prices[$type] ?? 80.00;
    }
}

<?php
// app/Http/Controllers/Admin/ClinicHoursController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClinicHoursController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Wyświetl stronę zarządzania godzinami kliniki
     */
    public function index()
    {
        // Pobierz godziny posortowane od poniedziałku
        $clinicHours = ClinicHours::getAllSorted();

        // Jeśli brak wpisów, utwórz domyślne
        if ($clinicHours->isEmpty()) {
            $this->createDefaultHours();
            $clinicHours = ClinicHours::getAllSorted();
        }

        return view('admin.clinic-hours.index', compact('clinicHours'));
    }

    /**
     * Aktualizuj godziny pracy kliniki
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hours' => 'required|array',
            'hours.*.day_of_week' => 'required|integer|min:0|max:6',
            'hours.*.start_time' => 'required|date_format:H:i',
            'hours.*.end_time' => 'required|date_format:H:i',
            'hours.*.is_active' => 'boolean',
        ], [
            'hours.required' => 'Dane godzin są wymagane.',
            'hours.*.start_time.required' => 'Godzina otwarcia jest wymagana.',
            'hours.*.start_time.date_format' => 'Nieprawidłowy format godziny otwarcia.',
            'hours.*.end_time.required' => 'Godzina zamknięcia jest wymagana.',
            'hours.*.end_time.date_format' => 'Nieprawidłowy format godziny zamknięcia.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($request->hours as $hourData) {
            // Walidacja: end_time musi być większy niż start_time (jeśli aktywny)
            $isActive = isset($hourData['is_active']) && $hourData['is_active'];

            if ($isActive && $hourData['start_time'] >= $hourData['end_time']) {
                return redirect()->back()
                    ->withErrors(['hours' => 'Godzina zamknięcia musi być późniejsza niż godzina otwarcia dla dnia ' . ClinicHours::DAY_NAMES[$hourData['day_of_week']]])
                    ->withInput();
            }

            ClinicHours::updateOrCreate(
                ['day_of_week' => $hourData['day_of_week']],
                [
                    'start_time' => $hourData['start_time'],
                    'end_time' => $hourData['end_time'],
                    'is_active' => $isActive,
                ]
            );
        }

        return redirect()->route('admin.clinic-hours.index')
            ->with('success', 'Godziny pracy kliniki zostały zaktualizowane.');
    }

    /**
     * Przywróć domyślne godziny pracy
     */
    public function setDefault()
    {
        $this->createDefaultHours();

        return redirect()->route('admin.clinic-hours.index')
            ->with('success', 'Przywrócono domyślne godziny pracy kliniki.');
    }

    /**
     * Utwórz domyślne godziny pracy
     */
    private function createDefaultHours(): void
    {
        $defaultHours = [
            // Poniedziałek - Piątek: 07:00 - 21:00
            ['day_of_week' => 1, 'start_time' => '07:00', 'end_time' => '21:00', 'is_active' => true],
            ['day_of_week' => 2, 'start_time' => '07:00', 'end_time' => '21:00', 'is_active' => true],
            ['day_of_week' => 3, 'start_time' => '07:00', 'end_time' => '21:00', 'is_active' => true],
            ['day_of_week' => 4, 'start_time' => '07:00', 'end_time' => '21:00', 'is_active' => true],
            ['day_of_week' => 5, 'start_time' => '07:00', 'end_time' => '21:00', 'is_active' => true],
            // Sobota: 08:00 - 16:00
            ['day_of_week' => 6, 'start_time' => '08:00', 'end_time' => '16:00', 'is_active' => true],
            // Niedziela: zamknięte
            ['day_of_week' => 0, 'start_time' => '00:00', 'end_time' => '00:00', 'is_active' => false],
        ];

        foreach ($defaultHours as $hours) {
            ClinicHours::updateOrCreate(
                ['day_of_week' => $hours['day_of_week']],
                $hours
            );
        }
    }
}

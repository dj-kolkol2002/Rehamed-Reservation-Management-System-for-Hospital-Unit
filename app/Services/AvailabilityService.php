<?php
// app/Services/AvailabilityService.php

namespace App\Services;

use App\Models\DoctorSchedule;
use App\Models\Appointment;
use App\Models\User;
use App\Models\SlotAvailability;
use App\Models\BlockedSlot;
use App\Models\ClinicHours;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Pobierz wszystkie dostępne sloty dla doktora w określonym przedziale czasu
     *
     * @param User $doctor
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $durationMinutes
     * @return array
     */
    public function getAvailableSlots(User $doctor, Carbon $startDate, Carbon $endDate, int $durationMinutes = 60): array
    {
        if ($doctor->role !== 'doctor') {
            return [];
        }

        $allSlots = [];
        $currentDate = $startDate->clone();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;

            // Sprawdź czy klinika jest otwarta w ten dzień
            $clinicHours = ClinicHours::where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (!$clinicHours) {
                $currentDate->addDay();
                continue; // Klinika zamknięta - pomiń ten dzień
            }

            // Znajdź harmonogram dla tego dnia
            $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            if ($schedule) {
                $daySlots = $schedule->getAvailableSlots($currentDate, $durationMinutes);

                // Filtruj sloty: wolne DLA TEGO LEKARZA i w godzinach otwarcia kliniki
                // WAŻNE: Przekazujemy doctor->id żeby sprawdzać tylko wizyty tego lekarza
                $filteredSlots = array_filter($daySlots, function ($slot) use ($clinicHours, $doctor) {
                    return $this->isSlotFree($slot['start'], $slot['end'], null, $doctor->id)
                        && $clinicHours->isSlotWithinHours($slot['start'], $slot['end']);
                });

                if (!empty($filteredSlots)) {
                    $allSlots[$currentDate->format('Y-m-d')] = array_values($filteredSlots);
                }
            }

            $currentDate->addDay();
        }

        return $allSlots;
    }

    /**
     * Sprawdź czy dany slot jest wolny (brak rezerwacji)
     * NAPRAWIONE: Konwertuje czasy do UTC aby porównywać z Appointments
     *
     * @param Carbon $startTime - Slot start (w Europe/Warsaw)
     * @param Carbon $endTime - Slot end (w Europe/Warsaw)
     * @param int|null $excludeAppointmentId - ID wizyty do wyłączenia z weryfikacji
     * @param int|null $doctorId - ID lekarza (opcjonalne - sprawdza dla konkretnego lekarza)
     * @return bool
     */
    public function isSlotFree(Carbon $startTime, Carbon $endTime, ?int $excludeAppointmentId = null, ?int $doctorId = null): bool
    {
        // Konwertuj sloty z Europe/Warsaw do UTC dla porównania z Appointments
        $startUtc = $startTime->copy()->setTimezone('UTC');
        $endUtc = $endTime->copy()->setTimezone('UTC');

        $query = Appointment::where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startUtc, $endUtc) {
                $query->whereBetween('start_time', [$startUtc, $endUtc])
                    ->orWhereBetween('end_time', [$startUtc, $endUtc])
                    ->orWhere(function ($q) use ($startUtc, $endUtc) {
                        $q->where('start_time', '<=', $startUtc)
                            ->where('end_time', '>=', $endUtc);
                    });
            });

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return !$query->exists();
    }

    /**
     * Pobierz dostępne sloty dla dnia - bardziej szczegółowe
     *
     * @param User $doctor
     * @param Carbon $date
     * @param int $durationMinutes
     * @return array
     */
    public function getAvailableSlotsForDay(User $doctor, Carbon $date, int $durationMinutes = 60): array
    {
        $dayOfWeek = $date->dayOfWeek;

        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return [];
        }

        $slots = $schedule->getAvailableSlots($date, $durationMinutes);

        // Filtruj zarezerwowane sloty
        return array_values(array_filter($slots, function ($slot) {
            return $this->isSlotFree($slot['start'], $slot['end']);
        }));
    }

    /**
     * Sprawdź czy slot mieści się w godzinach otwarcia kliniki
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return bool
     */
    public function isWithinClinicHours(Carbon $startTime, Carbon $endTime): bool
    {
        $clinicHours = ClinicHours::where('day_of_week', $startTime->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$clinicHours) {
            return false; // Klinika zamknięta tego dnia
        }

        return $clinicHours->isSlotWithinHours($startTime, $endTime);
    }

    /**
     * Sprawdź czy jest dostępny konkretny slot
     *
     * @param User $doctor
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return bool
     */
    public function canBookSlot(User $doctor, Carbon $startTime, Carbon $endTime): bool
    {
        // Sprawdzenie godzin otwarcia kliniki
        if (!$this->isWithinClinicHours($startTime, $endTime)) {
            return false;
        }

        // Sprawdzenie czy harmonogram istnieje
        $dayOfWeek = $startTime->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return false;
        }

        // Sprawdzenie czy slot mieści się w harmonogramie
        if (!$schedule->isTimeSlotAvailable($startTime, $endTime)) {
            return false;
        }

        // Sprawdzenie czy slot jest wolny
        return $this->isSlotFree($startTime, $endTime);
    }

    /**
     * Pobierz wszystkie dni gdy doktor pracuje
     *
     * @param User $doctor
     * @return array
     */
    public function getWorkingDays(User $doctor): array
    {
        return DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get()
            ->map(function ($schedule) {
                return [
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $schedule->day_name,
                    'start_time' => $schedule->start_time ? $schedule->start_time->format('H:i') : null,
                    'end_time' => $schedule->end_time ? $schedule->end_time->format('H:i') : null,
                    'break_start' => $schedule->break_start ? $schedule->break_start->format('H:i') : null,
                    'break_end' => $schedule->break_end ? $schedule->break_end->format('H:i') : null,
                    'appointment_duration' => $schedule->appointment_duration
                ];
            })
            ->toArray();
    }

    /**
     * Pobierz następny dostępny slot dla doktora
     *
     * @param User $doctor
     * @param int $durationMinutes
     * @return array|null
     */
    public function getNextAvailableSlot(User $doctor, int $durationMinutes = 60): ?array
    {
        $startDate = now();
        $endDate = now()->addMonths(3); // Szukaj do 3 miesięcy w przód

        $slots = $this->getAvailableSlots($doctor, $startDate, $endDate, $durationMinutes);

        if (empty($slots)) {
            return null;
        }

        // Zwróć pierwszy dostępny slot
        foreach ($slots as $date => $dateSlots) {
            if (!empty($dateSlots)) {
                return [
                    'date' => $date,
                    'slot' => $dateSlots[0]
                ];
            }
        }

        return null;
    }

    /**
     * Pobierz godziny pracy doktora dla danego dnia
     *
     * @param User $doctor
     * @param Carbon $date
     * @return array|null
     */
    public function getScheduleForDay(User $doctor, Carbon $date): ?array
    {
        $dayOfWeek = $date->dayOfWeek;

        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return null;
        }

        return [
            'day_name' => $schedule->day_name,
            'start_time' => $schedule->start_time ? $schedule->start_time->format('H:i') : null,
            'end_time' => $schedule->end_time ? $schedule->end_time->format('H:i') : null,
            'break_start' => $schedule->break_start ? $schedule->break_start->format('H:i') : null,
            'break_end' => $schedule->break_end ? $schedule->break_end->format('H:i') : null,
            'appointment_duration' => $schedule->appointment_duration,
            'is_active' => $schedule->is_active
        ];
    }

    /**
     * ========================================
     * NOWY SYSTEM REZERWACJI
     * ========================================
     */

    /**
     * Pobierz dostępne sloty dla pacjenta (tylko public i restricted)
     *
     * @param User $doctor
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $patientId
     * @return array
     */
    public function getAvailableSlotsForPatient(User $doctor, Carbon $startDate, Carbon $endDate, ?int $patientId = null): array
    {
        $slots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->available()
            ->when($patientId, function($query) use ($patientId) {
                return $query->forPatient($patientId);
            }, function($query) {
                return $query->public();
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Grupuj po datach
        $groupedSlots = [];
        foreach ($slots as $slot) {
            $date = $slot->date->format('Y-m-d');

            if (!isset($groupedSlots[$date])) {
                $groupedSlots[$date] = [];
            }

            // Sprawdź czy slot nie jest zablokowany
            if (!$this->isSlotBlocked($doctor, $slot->getStartDateTime(), $slot->getEndDateTime())) {
                $groupedSlots[$date][] = [
                    'id' => $slot->id,
                    'start' => $slot->getStartDateTime(),
                    'end' => $slot->getEndDateTime(),
                    'time' => $slot->start_time . ' - ' . $slot->end_time,
                    'available_spots' => $slot->max_patients - $slot->current_bookings,
                    'max_patients' => $slot->max_patients
                ];
            }
        }

        return $groupedSlots;
    }

    /**
     * Sprawdź czy slot jest zablokowany
     *
     * @param User $doctor
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return bool
     */
    public function isSlotBlocked(User $doctor, Carbon $startTime, Carbon $endTime): bool
    {
        return BlockedSlot::forDoctor($doctor->id)
            ->between($startTime, $endTime)
            ->exists();
    }

    /**
     * Generuj sloty automatycznie na podstawie harmonogramu doktora
     *
     * @param User $doctor
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $visibility
     * @param int $maxPatients
     * @return int Liczba utworzonych slotów
     */
    public function generateSlotsFromSchedule(
        User $doctor,
        Carbon $startDate,
        Carbon $endDate,
        string $visibility = 'public',
        int $maxPatients = 1
    ): int {
        $generated = 0;
        $currentDate = $startDate->clone();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;

            // Znajdź harmonogram dla tego dnia
            $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            if ($schedule) {
                $daySlots = $schedule->getAvailableSlots($currentDate, $schedule->appointment_duration);

                foreach ($daySlots as $slot) {
                    // Sprawdź czy slot już istnieje
                    $exists = SlotAvailability::forDoctor($doctor->id)
                        ->onDate($currentDate)
                        ->where('start_time', $slot['start']->format('H:i:s'))
                        ->exists();

                    if (!$exists) {
                        // Sprawdź czy slot nie jest zablokowany
                        if (!$this->isSlotBlocked($doctor, $slot['start'], $slot['end'])) {
                            SlotAvailability::create([
                                'doctor_id' => $doctor->id,
                                'date' => $currentDate->format('Y-m-d'),
                                'start_time' => $slot['start']->format('H:i:s'),
                                'end_time' => $slot['end']->format('H:i:s'),
                                'max_patients' => $maxPatients,
                                'current_bookings' => 0,
                                'is_available' => true,
                                'visibility' => $visibility
                            ]);
                            $generated++;
                        }
                    }
                }
            }

            $currentDate->addDay();
        }

        return $generated;
    }

    /**
     * Zablokuj slot dla pacjentów (zmień widoczność na hidden)
     *
     * @param int $slotId
     * @return bool
     */
    public function hideSlot(int $slotId): bool
    {
        $slot = SlotAvailability::find($slotId);

        if (!$slot) {
            return false;
        }

        return $slot->update(['visibility' => 'hidden']);
    }

    /**
     * Udostępnij slot publicznie
     *
     * @param int $slotId
     * @return bool
     */
    public function makeSlotPublic(int $slotId): bool
    {
        $slot = SlotAvailability::find($slotId);

        if (!$slot) {
            return false;
        }

        return $slot->update(['visibility' => 'public', 'is_available' => true]);
    }

    /**
     * Ogranicz dostęp do slotu dla wybranych pacjentów
     *
     * @param int $slotId
     * @param array $patientIds
     * @return bool
     */
    public function restrictSlotToPatients(int $slotId, array $patientIds): bool
    {
        $slot = SlotAvailability::find($slotId);

        if (!$slot) {
            return false;
        }

        return $slot->update([
            'visibility' => 'restricted',
            'allowed_patient_ids' => $patientIds,
            'is_available' => true
        ]);
    }

    /**
     * Sprawdź czy pacjent może zarezerwować slot
     *
     * @param int $slotId
     * @param int|null $patientId
     * @return bool
     */
    public function canPatientBookSlot(int $slotId, ?int $patientId = null): bool
    {
        $slot = SlotAvailability::find($slotId);

        if (!$slot) {
            return false;
        }

        // Sprawdź czy slot jest dostępny dla tego pacjenta
        if (!$slot->isAvailableFor($patientId)) {
            return false;
        }

        // Sprawdź czy slot nie jest zablokowany
        if ($this->isSlotBlocked($slot->doctor, $slot->getStartDateTime(), $slot->getEndDateTime())) {
            return false;
        }

        return true;
    }

    /**
     * Pobierz statystyki dostępności dla lekarza
     *
     * @param User $doctor
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getAvailabilityStats(User $doctor, Carbon $startDate, Carbon $endDate): array
    {
        $totalSlots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->count();

        $availableSlots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->available()
            ->count();

        $bookedSlots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('current_bookings', '>', 0)
            ->count();

        $publicSlots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->public()
            ->count();

        $blockedPeriods = BlockedSlot::forDoctor($doctor->id)
            ->between($startDate, $endDate)
            ->count();

        return [
            'total_slots' => $totalSlots,
            'available_slots' => $availableSlots,
            'booked_slots' => $bookedSlots,
            'public_slots' => $publicSlots,
            'blocked_periods' => $blockedPeriods,
            'utilization_rate' => $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100, 2) : 0
        ];
    }

    /**
     * Usuń przeszłe sloty (czyszczenie bazy)
     *
     * @param Carbon|null $beforeDate
     * @return int Liczba usuniętych slotów
     */
    public function cleanupOldSlots(?Carbon $beforeDate = null): int
    {
        $date = $beforeDate ?? now()->subMonths(3);

        return SlotAvailability::where('date', '<', $date->format('Y-m-d'))
            ->delete();
    }

    /**
     * Pobierz dostępne sloty od WSZYSTKICH aktywnych fizjoterapeutów
     * (bez ujawniania pacjentowi informacji o konkretnym fizjoterapeucie)
     * Korzysta z DoctorSchedule zamiast SlotAvailability
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $patientId
     * @return array
     */
    public function getAvailableSlotsForAllDoctors(Carbon $startDate, Carbon $endDate, ?int $patientId = null): array
    {
        // Pobierz wszystkich aktywnych fizjoterapeutów
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->get();

        $allSlots = [];
        $slotCounter = 1; // Unikalny identyfikator dla slotów

        foreach ($doctors as $doctor) {
            $doctorSlots = $this->getAvailableSlots($doctor, $startDate, $endDate);

            foreach ($doctorSlots as $date => $dateSlots) {
                if (!isset($allSlots[$date])) {
                    $allSlots[$date] = [];
                }

                foreach ($dateSlots as $slot) {
                    // Sprawdź czy slot o tym samym czasie już istnieje dla tego dnia
                    $timeKey = $slot['start']->format('H:i') . '-' . $slot['end']->format('H:i');
                    $existingIndex = null;

                    foreach ($allSlots[$date] as $index => $existingSlot) {
                        if ($existingSlot['time_key'] === $timeKey) {
                            $existingIndex = $index;
                            break;
                        }
                    }

                    if ($existingIndex !== null) {
                        // Dodaj doctor_id do istniejącego slotu
                        $allSlots[$date][$existingIndex]['doctor_ids'][] = $doctor->id;
                        $allSlots[$date][$existingIndex]['max_patients']++;
                    } else {
                        // Nowy slot
                        $allSlots[$date][] = [
                            'id' => 'slot_' . $slotCounter++,
                            'start_time' => $slot['start']->format('H:i'),
                            'end_time' => $slot['end']->format('H:i'),
                            'time_key' => $timeKey,
                            'max_patients' => 1,
                            'current_bookings' => 0,
                            'doctor_ids' => [$doctor->id], // Ukryte - do użytku wewnętrznego
                        ];
                    }
                }
            }
        }

        // Sortuj sloty po czasie w każdym dniu
        foreach ($allSlots as $date => &$dateSlots) {
            usort($dateSlots, function ($a, $b) {
                return strcmp($a['start_time'], $b['start_time']);
            });
        }

        // Sortuj daty
        ksort($allSlots);

        return $allSlots;
    }

    /**
     * Pobierz pierwszy dostępny slot od DOWOLNEGO aktywnego fizjoterapeuty
     * Korzysta z DoctorSchedule zamiast SlotAvailability
     *
     * @param int|null $patientId
     * @return array|null
     */
    public function getNextAvailableSlotForAnyDoctor(?int $patientId = null): ?array
    {
        // Pobierz wszystkich aktywnych fizjoterapeutów
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->get();

        if ($doctors->isEmpty()) {
            return null;
        }

        $startDate = now();
        $endDate = now()->addMonths(3);

        $earliestSlot = null;
        $earliestDoctorId = null;

        foreach ($doctors as $doctor) {
            $nextSlot = $this->getNextAvailableSlot($doctor);

            if ($nextSlot) {
                $slotDateTime = Carbon::parse($nextSlot['date'] . ' ' . $nextSlot['slot']['start']->format('H:i:s'));

                // Sprawdź czy to jest wcześniejszy slot
                if ($earliestSlot === null || $slotDateTime->lt(Carbon::parse($earliestSlot['date'] . ' ' . $earliestSlot['start_time']))) {
                    $earliestSlot = [
                        'date' => $nextSlot['date'],
                        'start_time' => $nextSlot['slot']['start']->format('H:i'),
                        'end_time' => $nextSlot['slot']['end']->format('H:i'),
                    ];
                    $earliestDoctorId = $doctor->id;
                }
            }
        }

        if (!$earliestSlot) {
            return null;
        }

        // Generuj unikalny identyfikator dla slotu
        $slotId = 'suggested_' . $earliestDoctorId . '_' . str_replace(['-', ':'], '', $earliestSlot['date'] . $earliestSlot['start_time']);

        return [
            'id' => $slotId,
            'date' => $earliestSlot['date'],
            'start_time' => $earliestSlot['start_time'],
            'end_time' => $earliestSlot['end_time'],
            'doctor_id' => $earliestDoctorId, // Ukryte - do użytku wewnętrznego
        ];
    }
}

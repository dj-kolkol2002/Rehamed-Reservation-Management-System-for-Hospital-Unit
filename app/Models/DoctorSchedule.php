<?php
// app/Models/DoctorSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'appointment_duration',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Relacja z doktorem
     * Uwzględnia także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->withTrashed();
    }

    /**
     * Nazwy dni tygodnia
     */
    public static function getDayNames(): array
    {
        return [
            0 => 'Niedziela',
            1 => 'Poniedziałek',
            2 => 'Wtorek',
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota'
        ];
    }

    /**
     * Pobierz nazwę dnia dla tego harmonogramu
     */
    public function getDayNameAttribute(): string
    {
        return self::getDayNames()[$this->day_of_week] ?? 'Nieznany';
    }

    /**
     * Metoda do pobierania nazwy dnia
     */
    public function getDayName(): string
    {
        return self::getDayNames()[$this->day_of_week] ?? 'Nieznany';
    }

    /**
     * HELPER: Pobierz czas jako Carbon w Europe/Warsaw timezone
     * Konwertuje stored time (H:i) na pełny Carbon z datą i timezone
     * WAŻNE: Najpierw konwertuj datę na Warsaw, potem ustaw czas!
     *
     * @param Carbon|null $storedTime - Stored H:i
     * @param Carbon $date - Data do użycia (w dowolnym timezone)
     * @return Carbon|null
     */
    private function getTimeInWarsawTimezone(?Carbon $storedTime, Carbon $date): ?Carbon
    {
        if (!$storedTime) {
            return null;
        }

        // WAŻNE: Konwertuj datę na Warsaw NAJPIERW, potem ustaw czas
        // Jeśli zrobimy setTimezone na końcu, przesunie godzinę!
        return $date->clone()
            ->setTimezone('Europe/Warsaw')  // ← Najpierw do Warsaw
            ->setTimeFromTimeString($storedTime->format('H:i:s'));  // ← Potem ustaw czas
    }

    /**
     * Pobierz wszystkie dostępne sloty dla danego dnia
     * NAPRAWIONE: Konsekwentnie pracuje w Europe/Warsaw timezone
     *
     * @param Carbon $date - Data do sprawdzenia (w dowolnej strefie)
     * @param int $durationMinutes - Długość slotu (jeśli inna niż w schedulu)
     * @return array - Sloty w Europe/Warsaw timezone
     */
    public function getAvailableSlots(Carbon $date, ?int $durationMinutes = null): array
    {
        // Konwertuj datę do Europe/Warsaw jeśli potrzeba
        $date = $date->copy()->setTimezone('Europe/Warsaw');

        // Jeśli dzień nie zgadza się z dniem tygodnia, zwróć pusty array
        if ($date->dayOfWeek !== $this->day_of_week) {
            return [];
        }

        if (!$this->is_active) {
            return [];
        }

        $duration = $durationMinutes ?? $this->appointment_duration;
        $slots = [];

        // Konwertuj czas harmonogramu na pełne Carbon w Europe/Warsaw
        $startTime = $this->getTimeInWarsawTimezone($this->start_time, $date);
        $endTime = $this->getTimeInWarsawTimezone($this->end_time, $date);

        if (!$startTime || !$endTime) {
            return [];
        }

        $currentTime = $startTime->clone();

        while ($currentTime->copy()->addMinutes($duration)->lte($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($duration);

            // Utwórz slot z pełną datą i czasem w Europe/Warsaw
            $slots[] = [
                'start' => $currentTime->copy(),
                'end' => $slotEnd->copy(),
                'time' => $currentTime->format('H:i') . ' - ' . $slotEnd->format('H:i')
            ];

            $currentTime->addMinutes($duration);
        }

        return $slots;
    }

    /**
     * Pobierz dostępne sloty na wiele dni
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getAvailableSlotsRange(Carbon $startDate, Carbon $endDate): array
    {
        $allSlots = [];
        $currentDate = $startDate->clone();

        while ($currentDate->lte($endDate)) {
            $daySlots = $this->getAvailableSlots($currentDate);
            if (!empty($daySlots)) {
                $allSlots[$currentDate->format('Y-m-d')] = $daySlots;
            }
            $currentDate->addDay();
        }

        return $allSlots;
    }

    /**
     * Sprawdź czy dany czas jest dostępny (czy nie ma już wizyty)
     */
    public function isTimeSlotAvailable(Carbon $startTime, Carbon $endTime): bool
    {
        // Sprawdź czy slot mieści się w godzinach pracy
        $dayOfWeek = $startTime->dayOfWeek;
        if ($dayOfWeek !== $this->day_of_week) {
            return false;
        }

        if (!$this->start_time || !$this->end_time) {
            return false;
        }

        $scheduleStart = Carbon::createFromFormat('H:i', $this->start_time->format('H:i'));
        $scheduleEnd = Carbon::createFromFormat('H:i', $this->end_time->format('H:i'));

        $slotStart = Carbon::createFromFormat('H:i', $startTime->format('H:i'));
        $slotEnd = Carbon::createFromFormat('H:i', $endTime->format('H:i'));

        // Slot musi być całkowicie w godzinach pracy
        if ($slotStart->lt($scheduleStart) || $slotEnd->gt($scheduleEnd)) {
            return false;
        }

        // Sprawdź czy nie ma już wizyty w tym słocie
        // WAŻNE: Konwertuj na UTC przed zapytaniem do bazy (Appointment przechowuje daty w UTC)
        $startTimeUtc = $startTime->copy()->setTimezone('UTC');
        $endTimeUtc = $endTime->copy()->setTimezone('UTC');

        $conflictingAppointments = Appointment::where('doctor_id', $this->doctor_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTimeUtc, $endTimeUtc) {
                $query->whereBetween('start_time', [$startTimeUtc, $endTimeUtc])
                    ->orWhereBetween('end_time', [$startTimeUtc, $endTimeUtc])
                    ->orWhere(function ($q) use ($startTimeUtc, $endTimeUtc) {
                        $q->where('start_time', '<=', $startTimeUtc)
                            ->where('end_time', '>=', $endTimeUtc);
                    });
            })
            ->exists();

        return !$conflictingAppointments;
    }
}

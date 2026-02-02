<?php
// app/Models/ClinicHours.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClinicHours extends Model
{
    use HasFactory;

    protected $table = 'clinic_hours';

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Polskie nazwy dni tygodnia
     */
    public const DAY_NAMES = [
        0 => 'Niedziela',
        1 => 'Poniedziałek',
        2 => 'Wtorek',
        3 => 'Środa',
        4 => 'Czwartek',
        5 => 'Piątek',
        6 => 'Sobota',
    ];

    /**
     * Accessor - polska nazwa dnia tygodnia
     */
    public function getDayNameAttribute(): string
    {
        return self::DAY_NAMES[$this->day_of_week] ?? 'Nieznany';
    }

    /**
     * Sprawdza czy klinika jest otwarta o podanej godzinie
     */
    public function isOpenAt(Carbon $time): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($time->dayOfWeek !== $this->day_of_week) {
            return false;
        }

        $checkTime = Carbon::createFromFormat('H:i', $time->format('H:i'));
        $openTime = Carbon::createFromFormat('H:i', $this->start_time->format('H:i'));
        $closeTime = Carbon::createFromFormat('H:i', $this->end_time->format('H:i'));

        return $checkTime->gte($openTime) && $checkTime->lt($closeTime);
    }

    /**
     * Sprawdza czy slot czasowy mieści się w godzinach otwarcia kliniki
     */
    public function isSlotWithinHours(Carbon $startTime, Carbon $endTime): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($startTime->dayOfWeek !== $this->day_of_week) {
            return false;
        }

        $slotStart = Carbon::createFromFormat('H:i', $startTime->format('H:i'));
        $slotEnd = Carbon::createFromFormat('H:i', $endTime->format('H:i'));
        $clinicOpen = Carbon::createFromFormat('H:i', $this->start_time->format('H:i'));
        $clinicClose = Carbon::createFromFormat('H:i', $this->end_time->format('H:i'));

        return $slotStart->gte($clinicOpen) && $slotEnd->lte($clinicClose);
    }

    /**
     * Scope - tylko aktywne dni
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope - filtrowanie po dniu tygodnia
     */
    public function scopeForDay($query, int $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Pobierz godziny dla konkretnego dnia
     */
    public static function getForDay(int $dayOfWeek): ?self
    {
        return self::forDay($dayOfWeek)->first();
    }

    /**
     * Sprawdź czy klinika jest otwarta w podanym dniu i godzinie (statyczna metoda pomocnicza)
     */
    public static function isClinicOpen(Carbon $startTime, Carbon $endTime): bool
    {
        $clinicHours = self::forDay($startTime->dayOfWeek)->first();

        if (!$clinicHours) {
            return false;
        }

        return $clinicHours->isSlotWithinHours($startTime, $endTime);
    }

    /**
     * Pobierz wszystkie godziny posortowane od poniedziałku
     */
    public static function getAllSorted()
    {
        return self::orderByRaw('CASE WHEN day_of_week = 0 THEN 7 ELSE day_of_week END')->get();
    }
}

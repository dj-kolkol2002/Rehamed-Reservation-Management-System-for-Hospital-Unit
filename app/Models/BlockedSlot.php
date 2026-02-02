<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BlockedSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'start_time',
        'end_time',
        'reason',
        'notes',
        'is_recurring',
        'recurrence_pattern'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'array'
    ];

    /**
     * Relacja z doktorem
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->withTrashed();
    }

    /**
     * Pobierz nazwę powodu w języku polskim
     */
    public function getReasonDisplayAttribute()
    {
        $reasons = [
            'personal' => 'Sprawy osobiste',
            'vacation' => 'Urlop',
            'sick_leave' => 'Zwolnienie lekarskie',
            'training' => 'Szkolenie',
            'emergency' => 'Nagły wypadek',
            'other' => 'Inny'
        ];

        return $reasons[$this->reason] ?? 'Nieznany';
    }

    /**
     * Sprawdź czy blokada koliduje z określonym zakresem czasu
     */
    public function overlaps(Carbon $startTime, Carbon $endTime): bool
    {
        return $this->start_time < $endTime && $this->end_time > $startTime;
    }

    /**
     * Sprawdź czy blokada jest aktywna w danym dniu
     */
    public function isActiveOn(Carbon $date): bool
    {
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();

        return $this->start_time <= $dateEnd && $this->end_time >= $dateStart;
    }

    /**
     * Scopes
     */
    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeActive($query)
    {
        return $query->where('end_time', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }

    public function scopeBetween($query, Carbon $startTime, Carbon $endTime)
    {
        return $query->where(function($q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime)
              ->where('end_time', '>', $startTime);
        });
    }

    public function scopeOnDate($query, Carbon $date)
    {
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();

        return $query->where('start_time', '<=', $dateEnd)
                     ->where('end_time', '>=', $dateStart);
    }

    /**
     * Generuj powtarzające się blokady
     * Wzór: ['type' => 'weekly', 'days' => [1, 3, 5], 'end_date' => '2025-12-31']
     */
    public static function createRecurring(array $data)
    {
        $pattern = $data['recurrence_pattern'];
        $blocks = [];

        if ($pattern['type'] === 'weekly') {
            $currentDate = Carbon::parse($data['start_time']);
            $endDate = Carbon::parse($pattern['end_date']);

            while ($currentDate->lte($endDate)) {
                if (in_array($currentDate->dayOfWeek, $pattern['days'])) {
                    $blocks[] = self::create([
                        'doctor_id' => $data['doctor_id'],
                        'start_time' => $currentDate->copy(),
                        'end_time' => $currentDate->copy()->addHours(
                            Carbon::parse($data['start_time'])->diffInHours(Carbon::parse($data['end_time']))
                        ),
                        'reason' => $data['reason'],
                        'notes' => $data['notes'] ?? null,
                        'is_recurring' => true,
                        'recurrence_pattern' => $pattern
                    ]);
                }
                $currentDate->addDay();
            }
        }

        return $blocks;
    }
}

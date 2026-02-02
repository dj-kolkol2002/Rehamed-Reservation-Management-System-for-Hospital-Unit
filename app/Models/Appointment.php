<?php
// app/Models/Appointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'type',
        'start_time',
        'end_time',
        'doctor_id',
        'patient_id',
        'notes',
        'status',
        'color',
        'metadata',
        'price',
        'reservation_type',
        'reservation_status',
        'priority',
        'patient_can_cancel',
        'confirmed_at',
        'rejected_at',
        'rejection_reason'
    ];

    /**
     * The attributes that should be cast.
     * WAŻNE: NIE CASTUJ start_time i end_time jako 'datetime'!
     * Accessory obsługują konwersję UTC → Europe/Warsaw
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'price' => 'decimal:2',
            'patient_can_cancel' => 'boolean',
            'confirmed_at' => 'datetime',
            'rejected_at' => 'datetime'
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
     * Relacja z pacjentem
     * Uwzględnia także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id')->withTrashed();
    }

    /**
     * Relacja z płatnością
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relacja z harmonogramem doktora
     */
    public function doctorSchedule()
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_id', 'doctor_id')
            ->where('day_of_week', $this->start_time->dayOfWeek);
    }

    /**
     * Sprawdź czy wizyta została opłacona
     */
    public function isPaid(): bool
    {
        return $this->payment && $this->payment->isPaid();
    }

    /**
     * Sprawdź czy wizyta ma oczekującą płatność
     */
    public function hasPendingPayment(): bool
    {
        return $this->payment && $this->payment->isPending();
    }

    /**
     * Pobierz status płatności
     */
    public function getPaymentStatusAttribute()
    {
        if (!$this->payment) {
            return 'unpaid';
        }
        return $this->payment->status;
    }

    /**
     * Pobierz nazwę typu w języku polskim
     */
    public function getTypeDisplayAttribute()
    {
        $types = [
            'fizjoterapia' => 'Fizjoterapia',
            'konsultacja' => 'Konsultacja',
            'masaz' => 'Masaż leczniczy',
            'neurorehabilitacja' => 'Neurorehabilitacja',
            'kontrola' => 'Wizyta kontrolna'
        ];

        return $types[$this->type] ?? 'Nieznany';
    }

    /**
     * Pobierz nazwę statusu w języku polskim
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'scheduled' => 'Zaplanowana',
            'completed' => 'Zakończona',
            'cancelled' => 'Anulowana',
            'no_show' => 'Nieobecność'
        ];

        return $statuses[$this->status] ?? 'Nieznany';
    }

    /**
     * Pobierz kolor dla typu wizyty
     */
    public function getDefaultColorAttribute()
    {
        if ($this->color) {
            return $this->color;
        }

        $colors = [
            'fizjoterapia' => '#10b981',
            'konsultacja' => '#3b82f6',
            'masaz' => '#6366f1',
            'neurorehabilitacja' => '#f59e0b',
            'kontrola' => '#ec4899'
        ];

        return $colors[$this->type] ?? '#3b82f6';
    }

    /**
     * Pobierz czas trwania w minutach
     */
    public function getDurationInMinutesAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Sprawdź czy wizyta jest dziś
     */
    public function getIsTodayAttribute()
    {
        return $this->start_time->isToday();
    }

    /**
     * Sprawdź czy wizyta jest w przyszłości
     */
    public function getIsFutureAttribute()
    {
        return $this->start_time->isFuture();
    }

    /**
     * Sprawdź czy wizyta jest w przeszłości
     */
    public function getIsPastAttribute()
    {
        return $this->start_time->isPast();
    }

    /**
     * Sprawdź czy użytkownik może przeglądać wizytę
     */
    public function canBeViewedBy($user)
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'doctor' && $this->doctor_id === $user->id) {
            return true;
        }
        if ($user->role === 'user' && $this->patient_id === $user->id) {
            return true;
        }
        return false;
    }

    /**
     * Sprawdź czy użytkownik może edytować wizytę
     */
    public function canBeEditedBy($user)
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'doctor' && $this->doctor_id === $user->id) {
            return true;
        }
        return false;
    }

    /**
     * Sprawdź czy użytkownik może anulować wizytę
     */
    public function canBeCancelledBy($user)
    {
        if ($this->status === 'completed') {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'doctor' && $this->doctor_id === $user->id) {
            return true;
        }
        if ($user->role === 'user' && $this->patient_id === $user->id && $this->is_future) {
            return true;
        }
        return false;
    }

    /**
     * Scope dla wizyt doktora
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope dla wizyt pacjenta
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope dla wizyt w określonym przedziale czasowym
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where('start_time', '>=', $startDate)
                    ->where('start_time', '<=', $endDate);
    }

    /**
     * Scope dla wizyt według statusu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope dla zaplanowanych wizyt
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope dla dzisiejszych wizyt
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    /**
     * Scope dla przyszłych wizyt
     */
    public function scopeFuture($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope dla wizyt z tego tygodnia
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope dla wizyt z tego miesiąca
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('start_time', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Accessor: Konwertuj start_time z UTC (baza) do Europe/Warsaw
     * Dane w bazie SĄ w UTC z migracji 2025_12_14_134012
     */
    public function getStartTimeAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value, 'UTC')->setTimezone('Europe/Warsaw');
        }
        return null;
    }

    /**
     * Mutator: Konwertuj start_time z Europe/Warsaw do UTC przed zapisem
     */
    public function setStartTimeAttribute($value)
    {
        if ($value) {
            if ($value instanceof Carbon) {
                $carbon = $value->clone();
            } else {
                $carbon = Carbon::parse($value);
            }
            
            $tz = $carbon->timezone?->getName() ?? null;
            
            if ($tz === 'UTC') {
                $this->attributes['start_time'] = $carbon->format('Y-m-d H:i:s');
            } elseif ($tz === 'Europe/Warsaw') {
                $this->attributes['start_time'] = $carbon->setTimezone('UTC')->format('Y-m-d H:i:s');
            } else {
                $this->attributes['start_time'] = $carbon->setTimezone('Europe/Warsaw')->setTimezone('UTC')->format('Y-m-d H:i:s');
            }
        }
    }

    /**
     * Accessor: Konwertuj end_time z UTC do Europe/Warsaw
     */
    public function getEndTimeAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value, 'UTC')->setTimezone('Europe/Warsaw');
        }
        return null;
    }

    /**
     * Mutator: Konwertuj end_time z Europe/Warsaw do UTC przed zapisem
     */
    public function setEndTimeAttribute($value)
    {
        if ($value) {
            if ($value instanceof Carbon) {
                $carbon = $value->clone();
            } else {
                $carbon = Carbon::parse($value);
            }
            
            $tz = $carbon->timezone?->getName() ?? null;
            
            if ($tz === 'UTC') {
                $this->attributes['end_time'] = $carbon->format('Y-m-d H:i:s');
            } elseif ($tz === 'Europe/Warsaw') {
                $this->attributes['end_time'] = $carbon->setTimezone('UTC')->format('Y-m-d H:i:s');
            } else {
                $this->attributes['end_time'] = $carbon->setTimezone('Europe/Warsaw')->setTimezone('UTC')->format('Y-m-d H:i:s');
            }
        }
    }

    /**
     * Formatuj dane do FullCalendar
     */
    public function toFullCalendarEvent()
    {
        $startTime = $this->start_time->toIso8601String();
        $endTime = $this->end_time->toIso8601String();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $startTime,
            'end' => $endTime,
            'allDay' => false,
            'backgroundColor' => $this->default_color,
            'borderColor' => $this->default_color,
            'textColor' => '#ffffff',
            'extendedProps' => [
                'type' => $this->type,
                'type_display' => $this->type_display,
                'status' => $this->status,
                'status_display' => $this->status_display,
                'doctor_id' => $this->doctor_id,
                'doctor_name' => $this->doctor ? $this->doctor->firstname . ' ' . $this->doctor->lastname : null,
                'patient_id' => $this->patient_id,
                'patient_name' => $this->patient ? $this->patient->firstname . ' ' . $this->patient->lastname : null,
                'notes' => $this->notes,
                'price' => $this->price,
                'duration' => $this->duration_in_minutes,
                'is_past' => $this->is_past,
                'is_today' => $this->is_today,
                'is_future' => $this->is_future
            ]
        ];
    }

    /**
     * Sprawdź czy są konflikty czasowe z innymi wizytami
     * WAŻNE: $this->start_time i $this->end_time są w Europe/Warsaw (z accessor)
     * Musimy porównać z danymi w bazie które są w UTC
     * Konwertuj timesze do UTC dla query
     */
    public function hasTimeConflict($excludeId = null)
    {
        $startTimeUTC = $this->start_time->copy()->setTimezone('UTC');
        $endTimeUTC = $this->end_time->copy()->setTimezone('UTC');

        $query = static::where('doctor_id', $this->doctor_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTimeUTC, $endTimeUTC) {
                $q->whereBetween('start_time', [$startTimeUTC, $endTimeUTC])
                  ->orWhereBetween('end_time', [$startTimeUTC, $endTimeUTC])
                  ->orWhere(function ($q2) use ($startTimeUTC, $endTimeUTC) {
                      $q2->where('start_time', '<=', $startTimeUTC)
                         ->where('end_time', '>=', $endTimeUTC);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Sprawdź czy pacjent ma konflikty czasowe z innymi wizytami (u innych fizjoterapeutów)
     */
    public function hasPatientTimeConflict($excludeId = null)
    {
        if (!$this->patient_id) {
            return false;
        }

        $startTimeUTC = $this->start_time->copy()->setTimezone('UTC');
        $endTimeUTC = $this->end_time->copy()->setTimezone('UTC');

        $query = static::where('patient_id', $this->patient_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->whereNull('reservation_status')
                  ->orWhere('reservation_status', '!=', 'pending');
            })
            ->where(function ($q) use ($startTimeUTC, $endTimeUTC) {
                $q->whereBetween('start_time', [$startTimeUTC, $endTimeUTC])
                  ->orWhereBetween('end_time', [$startTimeUTC, $endTimeUTC])
                  ->orWhere(function ($q2) use ($startTimeUTC, $endTimeUTC) {
                      $q2->where('start_time', '<=', $startTimeUTC)
                         ->where('end_time', '>=', $endTimeUTC);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Pobierz sąsiednie wizyty
     */
    public function getAdjacentAppointments()
    {
        $previous = static::where('doctor_id', $this->doctor_id)
            ->where('start_time', '<', $this->start_time)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time', 'desc')
            ->first();

        $next = static::where('doctor_id', $this->doctor_id)
            ->where('start_time', '>', $this->end_time)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time', 'asc')
            ->first();

        return [
            'previous' => $previous,
            'next' => $next
        ];
    }

    /**
     * ========================================
     * METODY SYSTEMU REZERWACJI
     * ========================================
     */

    /**
     * Sprawdź czy rezerwacja jest oczekującą
     */
    public function isPending(): bool
    {
        return $this->reservation_status === 'pending';
    }

    /**
     * Sprawdź czy rezerwacja została potwierdzona
     */
    public function isConfirmed(): bool
    {
        return $this->reservation_status === 'confirmed' ||
               $this->reservation_status === 'auto_confirmed';
    }

    /**
     * Sprawdź czy rezerwacja została odrzucona
     */
    public function isRejected(): bool
    {
        return $this->reservation_status === 'rejected';
    }

    /**
     * Sprawdź czy wizyta jest priorytetowa
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent' || $this->priority === 'emergency';
    }

    /**
     * Potwierdź rezerwację
     */
    public function confirm()
    {
        $this->update([
            'reservation_status' => 'confirmed',
            'confirmed_at' => now(),
            'status' => 'scheduled'
        ]);
    }

    /**
     * Odrzuć rezerwację
     */
    public function reject(string $reason = null)
    {
        $this->update([
            'reservation_status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'status' => 'cancelled'
        ]);
    }

    /**
     * Pobierz nazwę statusu rezerwacji w języku polskim
     */
    public function getReservationStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'Oczekuje na potwierdzenie',
            'confirmed' => 'Potwierdzona',
            'auto_confirmed' => 'Automatycznie potwierdzona',
            'rejected' => 'Odrzucona'
        ];

        return $statuses[$this->reservation_status] ?? 'Brak statusu';
    }

    /**
     * Pobierz nazwę typu rezerwacji
     */
    public function getReservationTypeDisplayAttribute()
    {
        $types = [
            'online' => 'Rezerwacja online',
            'phone' => 'Rezerwacja telefoniczna',
            'in_person' => 'Rejestracja osobista',
            'doctor_created' => 'Utworzona przez lekarza'
        ];

        return $types[$this->reservation_type] ?? 'Nieznany typ';
    }

    /**
     * Pobierz nazwę priorytetu
     */
    public function getPriorityDisplayAttribute()
    {
        $priorities = [
            'normal' => 'Normalny',
            'urgent' => 'Pilny',
            'emergency' => 'Nagły wypadek'
        ];

        return $priorities[$this->priority] ?? 'Normalny';
    }

    /**
     * Scopes dla rezerwacji
     */
    public function scopePending($query)
    {
        return $query->where('reservation_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->whereIn('reservation_status', ['confirmed', 'auto_confirmed']);
    }

    public function scopePatientRequests($query)
    {
        return $query->whereIn('reservation_type', ['online', 'phone', 'in_person'])
                    ->whereNotNull('patient_id');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['urgent', 'emergency']);
    }
}

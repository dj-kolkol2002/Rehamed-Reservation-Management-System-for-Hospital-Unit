<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SlotAvailability extends Model
{
    use HasFactory;

    protected $table = 'slot_availability';

    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'max_patients',
        'current_bookings',
        'is_available',
        'visibility',
        'allowed_patient_ids',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'allowed_patient_ids' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Relacja z doktorem
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->withTrashed();
    }

    /**
     * Sprawdź czy slot jest pełny
     */
    public function isFull(): bool
    {
        return $this->current_bookings >= $this->max_patients;
    }

    /**
     * Sprawdź czy slot jest dostępny dla pacjenta
     */
    public function isAvailableFor(?int $patientId = null): bool
    {
        // Sprawdź podstawową dostępność
        if (!$this->is_available || $this->isFull()) {
            return false;
        }

        // Sprawdź widoczność
        if ($this->visibility === 'hidden') {
            return false;
        }

        // Dla restricted sprawdź czy pacjent jest na liście
        if ($this->visibility === 'restricted' && $patientId) {
            $allowedIds = $this->allowed_patient_ids ?? [];
            return in_array($patientId, $allowedIds);
        }

        // Public - dostępny dla wszystkich
        return $this->visibility === 'public';
    }

    /**
     * Zwiększ licznik rezerwacji
     */
    public function incrementBookings()
    {
        $this->increment('current_bookings');

        // Jeśli osiągnięto limit, ustaw jako niedostępny
        if ($this->current_bookings >= $this->max_patients) {
            $this->update(['is_available' => false]);
        }
    }

    /**
     * Zmniejsz licznik rezerwacji
     */
    public function decrementBookings()
    {
        if ($this->current_bookings > 0) {
            $this->decrement('current_bookings');

            // Jeśli zwolniono miejsce, ustaw jako dostępny
            if ($this->current_bookings < $this->max_patients) {
                $this->update(['is_available' => true]);
            }
        }
    }

    /**
     * Dodaj pacjenta do listy dozwolonych (dla restricted)
     */
    public function addAllowedPatient(int $patientId)
    {
        $allowed = $this->allowed_patient_ids ?? [];

        if (!in_array($patientId, $allowed)) {
            $allowed[] = $patientId;
            $this->update(['allowed_patient_ids' => $allowed]);
        }
    }

    /**
     * Usuń pacjenta z listy dozwolonych
     */
    public function removeAllowedPatient(int $patientId)
    {
        $allowed = $this->allowed_patient_ids ?? [];
        $allowed = array_values(array_filter($allowed, fn($id) => $id != $patientId));
        $this->update(['allowed_patient_ids' => $allowed]);
    }

    /**
     * Scopes
     */
    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->where('date', Carbon::parse($date)->format('Y-m-d'));
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeForPatient($query, int $patientId)
    {
        return $query->where(function($q) use ($patientId) {
            $q->where('visibility', 'public')
              ->orWhere(function($q2) use ($patientId) {
                  $q2->where('visibility', 'restricted')
                     ->whereJsonContains('allowed_patient_ids', $patientId);
              });
        });
    }

    /**
     * Pobierz pełny DateTime dla początku slotu
     */
    public function getStartDateTime(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time);
    }

    /**
     * Pobierz pełny DateTime dla końca slotu
     */
    public function getEndDateTime(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time);
    }
}

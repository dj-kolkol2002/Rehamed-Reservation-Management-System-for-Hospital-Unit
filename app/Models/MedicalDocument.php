<?php
// app/Models/MedicalDocument.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MedicalDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'title',
        'type',
        'content',
        'notes',
        'file_path',
        'file_name',
        'status',
        'document_date',
        'metadata',
        'is_private',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'metadata' => 'array',
            'is_private' => 'boolean',
        ];
    }

    /**
     * Relacja z pacjentem (użytkownikiem)
     * Uwzględnia także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id')->withTrashed();
    }

    /**
     * Relacja z doktorem (użytkownikiem)
     * Uwzględnia także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->withTrashed();
    }

    /**
     * Sprawdź czy dokument ma załącznik
     */
    public function hasFile()
    {
        return $this->file_path && Storage::disk('private')->exists($this->file_path);
    }

    /**
     * Pobierz URL do pliku
     */
    public function getFileUrlAttribute()
    {
        if ($this->hasFile()) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    /**
     * Pobierz nazwę typu dokumentu w języku polskim
     */
    public function getTypeDisplayAttribute()
    {
        $types = [
            'general' => 'Ogólny',
            'diagnosis' => 'Diagnoza',
            'treatment' => 'Leczenie',
            'examination' => 'Badanie',
            'prescription' => 'Recepta',
        ];

        return $types[$this->type] ?? 'Nieznany';
    }

    /**
     * Pobierz nazwę statusu w języku polskim
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'draft' => 'Szkic',
            'completed' => 'Ukończony',
            'archived' => 'Zarchiwizowany',
        ];

        return $statuses[$this->status] ?? 'Nieznany';
    }

    /**
     * Pobierz kolor dla statusu
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'yellow',
            'completed' => 'green',
            'archived' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Sprawdź czy dokument może być przeglądany przez użytkownika
     */
    public function canBeViewedBy($user)
    {
        // Admin może zobaczyć wszystko
        if ($user->role === 'admin') {
            return true;
        }

        // Pacjent może zobaczyć tylko swoje dokumenty (ale nie prywatne)
        if ($user->role === 'user' && $user->id === $this->patient_id) {
            return !$this->is_private;
        }

        // Doktor może zobaczyć dokumenty swoich pacjentów
        if ($user->role === 'doctor' && $user->id === $this->doctor_id) {
            return true;
        }

        return false;
    }

    /**
     * Sprawdź czy dokument może być edytowany przez użytkownika
     */
    public function canBeEditedBy($user)
    {
        // Admin może edytować wszystko
        if ($user->role === 'admin') {
            return true;
        }

        // Doktor może edytować swoje dokumenty
        if ($user->role === 'doctor' && $user->id === $this->doctor_id) {
            return true;
        }

        return false;
    }

    /**
     * Scope dla dokumentów pacjenta
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope dla dokumentów doktora
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope dla publicznych dokumentów
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Scope dla prywatnych dokumentów
     */
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    /**
     * Scope dla dokumentów według statusu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope dla dokumentów ukończonych
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope dla szkiców dokumentów
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope dla zarchiwizowanych dokumentów
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Usuń plik załącznika
     */
    public function deleteFile()
    {
        if ($this->hasFile()) {
            Storage::disk('private')->delete($this->file_path);
            $this->update([
                'file_path' => null,
                'file_name' => null,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Zapisz nowy plik
     */
    public function saveFile($file)
    {
        // Usuń stary plik jeśli istnieje
        $this->deleteFile();

        // Zapisz nowy plik w private storage
        $filename = 'document_' . $this->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('medical-documents', $filename, 'private');

        if ($path) {
            $this->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            // Usuń plik przy usuwaniu dokumentu
            $document->deleteFile();
        });
    }
}

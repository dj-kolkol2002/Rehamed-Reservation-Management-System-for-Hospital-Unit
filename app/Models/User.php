<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'avatar',
        'role',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'medical_history',
        'preferences',
        'is_active',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'initials',
        'avatar_url',
        'role_display',
        'preferences_with_defaults'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'deleted_at' => 'datetime',
            'is_active' => 'boolean',
            'medical_history' => 'array',
            'preferences' => 'array'
        ];
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'date_of_birth',
    ];

    /**
     * Sprawdź czy użytkownik ma określoną rolę
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Sprawdź czy użytkownik jest administratorem
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Sprawdź czy użytkownik jest doktorem
     */
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    /**
     * Sprawdź czy użytkownik jest pacjentem
     */
    public function isPatient()
    {
        return $this->role === 'user';
    }

    /**
     * Pobierz pełne imię i nazwisko
     */
    public function getFullNameAttribute()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * Pobierz inicjały
     */
    public function getInitialsAttribute()
    {
        $firstname = $this->firstname ?? '';
        $lastname = $this->lastname ?? '';
        return strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
    }

    /**
     * Pobierz wyświetlaną nazwę roli
     */
    public function getRoleDisplayAttribute()
    {
        $roles = [
            'admin' => 'Administrator',
            'doctor' => 'Fizjoterapeuta',
            'user' => 'Pacjent'
        ];

        return $roles[$this->role] ?? 'Nieznana rola';
    }

    /**
     * Pobierz wyświetlaną nazwę roli (alternatywna metoda)
     */
    public function getRoleDisplayName()
    {
        return $this->getRoleDisplayAttribute();
    }

    /**
     * Pobierz URL avatara użytkownika
     */
    public function getAvatarUrlAttribute()
    {
        // Sprawdź czy użytkownik ma ustawiony avatar i plik istnieje
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Jeśli nie ma avatara, wygeneruj domyślny
        return $this->getDefaultAvatarUrl();
    }

    /**
     * Pobierz domyślny avatar na podstawie inicjałów
     */
    public function getDefaultAvatarUrl()
    {
        $name = urlencode($this->getInitialsAttribute());
        $background = $this->getAvatarColor();

        // Używa UI Avatars API do generowania domyślnych avatarów
        return "https://ui-avatars.com/api/?name={$name}&size=200&background={$background}&color=ffffff&bold=true&rounded=true";
    }

    /**
     * Pobierz kolor avatara na podstawie ID użytkownika
     */
    public function getAvatarColor()
    {
        $colors = [
            '667eea', '764ba2', 'f093fb', 'f5576c', 'ffd89b',
            '96fbc4', '74b9ff', '0984e3', 'a29bfe', '6c5ce7',
            'fd79a8', 'e84393', 'ff7675', 'd63031', 'fab1a0',
            'e17055', 'fdcb6e', 'e84393', '00b894', '00cec9'
        ];

        return $colors[$this->id % count($colors)];
    }

    /**
     * Sprawdź czy użytkownik ma własny avatar
     */
    public function hasCustomAvatar()
    {
        return $this->avatar && Storage::disk('public')->exists($this->avatar);
    }

    /**
     * Usuń avatar użytkownika
     */
    public function deleteAvatar()
    {
        if ($this->hasCustomAvatar()) {
            Storage::disk('public')->delete($this->avatar);
            $this->update(['avatar' => null]);
            return true;
        }

        return false;
    }

    /**
     * Zapisz nowy avatar
     */
    public function saveAvatar($file)
    {
        // Usuń stary avatar jeśli istnieje
        $this->deleteAvatar();

        // Zapisz nowy avatar w public disk
        $filename = $this->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        if ($path) {
            $this->update(['avatar' => $path]);
            return true;
        }

        return false;
    }

    /**
     * Pobierz wszystkich lekarzy
     */
    public static function getDoctors()
    {
        return self::where('role', 'doctor')->where('is_active', true)->get();
    }

    /**
     * Pobierz wszystkich pacjentów
     */
    public static function getPatients()
    {
        return self::where('role', 'user')->where('is_active', true)->get();
    }

    /**
     * Pobierz wszystkich administratorów
     */
    public static function getAdmins()
    {
        return self::where('role', 'admin')->where('is_active', true)->get();
    }

    /**
     * Sprawdź czy użytkownik może zarządzać innymi użytkownikami
     */
    public function canManageUsers()
    {
        return $this->isAdmin();
    }

    /**
     * Sprawdź czy użytkownik może wystawiać recepty
     */
    public function canPrescribe()
    {
        return $this->isDoctor() || $this->isAdmin();
    }

    /**
     * Sprawdź czy użytkownik może tworzyć wizyty
     */
    public function canCreateAppointments()
    {
        return $this->isDoctor() || $this->isAdmin();
    }

    /**
     * Sprawdź czy użytkownik może przeglądać wszystkie wizyty
     */
    public function canViewAllAppointments()
    {
        return $this->isAdmin();
    }

    /**
     * Relacje
     */

    /**
     * Wizyty jako pacjent
     */
    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Wizyty jako lekarz
     */
    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Powiadomienia
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Nieprzeczytane powiadomienia
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    /**
     * Dokumenty medyczne jako pacjent
     */
    public function patientMedicalDocuments()
    {
        return $this->hasMany(MedicalDocument::class, 'patient_id');
    }

    /**
     * Alias dla patientMedicalDocuments - dla kompatybilności
     */
    public function patientDocuments()
    {
        return $this->patientMedicalDocuments();
    }

    /**
     * Dokumenty medyczne jako lekarz
     */
    public function doctorMedicalDocuments()
    {
        return $this->hasMany(MedicalDocument::class, 'doctor_id');
    }

    /**
     * Alias dla doctorMedicalDocuments - dla kompatybilności
     */
    public function doctorDocuments()
    {
        return $this->doctorMedicalDocuments();
    }

    /**
     * Konwersacje gdzie użytkownik jest pierwszym uczestnikiem
     */
    public function conversationsAsUserOne()
    {
        return $this->hasMany(Conversation::class, 'user_one_id');
    }

    /**
     * Konwersacje gdzie użytkownik jest drugim uczestnikiem
     */
    public function conversationsAsUserTwo()
    {
        return $this->hasMany(Conversation::class, 'user_two_id');
    }

    /**
     * Wszystkie konwersacje użytkownika
     */
    public function conversations()
    {
        return $this->conversationsAsUserOne()
            ->union($this->conversationsAsUserTwo())
            ->orderBy('last_message_at', 'desc');
    }

    /**
     * Wysłane wiadomości
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Harmonogramy pracy (dla doktorów/fizjoterapeutów)
     */
    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopePatients($query)
    {
        return $query->where('role', 'user');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Metody pomocnicze dla chatu
     */

    /**
     * Pobierz użytkowników z którymi można rozpocząć chat
     */
    public function getChatableUsers()
    {
        $currentUserId = $this->id;
        $currentUserRole = $this->role;

        $query = self::where('id', '!=', $currentUserId)
                    ->where('is_active', true);

        // Logika uprawnień do chatu
        switch ($currentUserRole) {
            case 'admin':
                // Admin może pisać do wszystkich
                break;
            case 'doctor':
                // Doktor może pisać do pacjentów i adminów
                $query->whereIn('role', ['user', 'admin']);
                break;
            case 'user':
                // Pacjent może pisać tylko do doktorów i adminów
                $query->whereIn('role', ['doctor', 'admin']);
                break;
        }

        return $query->orderBy('firstname')->orderBy('lastname')->get();
    }

    /**
     * Sprawdź czy może pisać do określonego użytkownika
     */
    public function canChatWith($user)
    {
        if (!$user || !$user->is_active || $user->id === $this->id) {
            return false;
        }

        $currentRole = $this->role;
        $targetRole = $user->role;

        // Admin może pisać do wszystkich
        if ($currentRole === 'admin') {
            return true;
        }

        // Doktor może pisać do pacjentów i adminów
        if ($currentRole === 'doctor') {
            return in_array($targetRole, ['user', 'admin']);
        }

        // Pacjent może pisać tylko do doktorów i adminów
        if ($currentRole === 'user') {
            return in_array($targetRole, ['doctor', 'admin']);
        }

        return false;
    }

    /**
     * Pobierz liczbę nieprzeczytanych wiadomości
     */
    public function getUnreadMessagesCount()
    {
        $conversationIds = DB::table('conversations')
            ->where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id)
            ->pluck('id');

        return DB::table('messages')
            ->whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $this->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Pobierz preferencje z domyślnymi wartościami
     */
    public function getPreferencesWithDefaultsAttribute()
    {
        $defaults = [
            'theme' => 'light',
            'notifications' => true,
            'language' => 'pl'
        ];

        $userPreferences = $this->preferences ?? [];

        return array_merge($defaults, $userPreferences);
    }

    /**
     * Wyślij powiadomienie o weryfikacji email
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Wyślij powiadomienie o resetowaniu hasła
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    /**
     * Bootowanie modelu
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Ustaw domyślną rolę jeśli nie została podana
            if (empty($user->role)) {
                $user->role = 'user';
            }

            // Ustaw domyślną aktywność
            if ($user->is_active === null) {
                $user->is_active = true;
            }
        });

        static::deleting(function ($user) {
            // Soft delete - oznacz jako nieaktywny
            if (!$user->isForceDeleting()) {
                $user->is_active = false;
                $user->save();
            }
        });
    }
}

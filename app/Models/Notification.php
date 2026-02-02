<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'related_id',
        'related_type',
        'icon',
        'action_url'
    ];

    protected $appends = [
        'default_icon',
        'color',
        'formatted_time',
        'is_new'
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Relacja z użytkownikiem
     * Uwzględnia także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Polimorficzna relacja do powiązanego obiektu
    public function related()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Metody pomocnicze
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    // Pobierz kolor dla typu powiadomienia
    public function getColorAttribute()
    {
        $colors = [
            'appointment_created' => 'blue',
            'appointment_updated' => 'yellow',
            'appointment_cancelled' => 'red',
            'appointment_reminder' => 'orange',
            'document_created' => 'green',
            'document_updated' => 'yellow',
            'message_received' => 'purple',
            'user_registered' => 'indigo',
            'system' => 'gray',
        ];

        return $colors[$this->type] ?? 'gray';
    }

    // Pobierz domyślną ikonę dla typu powiadomienia
    public function getDefaultIconAttribute()
    {
        // Jeśli ikona jest zapisana bez prefixu 'fas fa-', dodaj go
        if ($this->icon) {
            if (!str_starts_with($this->icon, 'fas ') && !str_starts_with($this->icon, 'far ') && !str_starts_with($this->icon, 'fab ')) {
                return 'fas fa-' . $this->icon;
            }
            return $this->icon;
        }

        $icons = [
            'appointment_created' => 'fas fa-calendar-plus',
            'appointment_updated' => 'fas fa-pen',
            'appointment_cancelled' => 'fas fa-calendar-times',
            'appointment_reminder' => 'fas fa-clock',
            'document_created' => 'fas fa-file-medical',
            'document_updated' => 'fas fa-file-edit',
            'message_received' => 'fas fa-envelope',
            'user_registered' => 'fas fa-user-plus',
            'payment_completed' => 'fas fa-credit-card',
            'payment_received' => 'fas fa-money-bill-wave',
            'system' => 'fas fa-bell',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    // Pobierz sformatowany czas
    public function getFormattedTimeAttribute()
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('H:i');
        } elseif ($this->created_at->isYesterday()) {
            return 'Wczoraj';
        } elseif ($this->created_at->diffInDays() <= 7) {
            return $this->created_at->diffForHumans();
        } else {
            return $this->created_at->format('d.m.Y');
        }
    }

    // Sprawdź czy powiadomienie jest nowe (mniej niż 1 godzina)
    public function getIsNewAttribute()
    {
        return $this->created_at->diffInHours(now()) < 1;
    }

    // Statyczne metody do tworzenia powiadomień - NAPRAWIONE dla Laravel 12
    public static function createForUser($userId, $type, $title, $message, $data = [], $relatedModel = null)
    {
        // Walidacja parametrów
        if (!$userId || !$type || !$title || !$message) {
            Log::error('Nieprawidłowe parametry dla createForUser', [
                'userId' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message
            ]);
            return null;
        }

        try {
            return self::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'related_id' => $relatedModel && isset($relatedModel->id) ? $relatedModel->id : null,
                'related_type' => $relatedModel ? get_class($relatedModel) : null,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia powiadomienia: ' . $e->getMessage());
            return null;
        }
    }

    public static function createForMultipleUsers($userIds, $type, $title, $message, $data = [], $relatedModel = null)
    {
        // Walidacja parametrów
        if (empty($userIds) || !$type || !$title || !$message) {
            Log::error('Nieprawidłowe parametry dla createForMultipleUsers');
            return false;
        }

        $notifications = [];
        $relatedId = null;
        $relatedType = null;


        if ($relatedModel) {
            try {
                $relatedId = isset($relatedModel->id) ? $relatedModel->id : null;
                $relatedType = get_class($relatedModel);
            } catch (\Exception $e) {
                Log::warning('Problem z powiązanym modelem: ' . $e->getMessage());
            }
        }

        foreach ($userIds as $userId) {
            if (!$userId) continue;

            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'related_id' => $relatedId,
                'related_type' => $relatedType,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($notifications)) {
            return false;
        }

        try {
            return self::insert($notifications);
        } catch (\Exception $e) {
            Log::error('Błąd podczas wstawiania powiadomień: ' . $e->getMessage());
            return false;
        }
    }

    // Metody do typów powiadomień - NAPRAWIONE dla Laravel 12
    public static function appointmentCreated($appointment)
    {
        if (!$appointment || !isset($appointment->id)) {
            Log::error('Nieprawidłowy obiekt appointment w appointmentCreated');
            return;
        }

        try {

            if (isset($appointment->patient_id) && $appointment->patient_id) {
                self::createForUser(
                    $appointment->patient_id,
                    'appointment_created',
                    'Nowa wizyta',
                    "Umówiono Cię na wizytę: {$appointment->title} w dniu " . $appointment->start_time->format('d.m.Y H:i'),
                    ['appointment_id' => $appointment->id],
                    $appointment
                );
            }


            if (isset($appointment->doctor_id) && $appointment->doctor_id) {
                self::createForUser(
                    $appointment->doctor_id,
                    'appointment_created',
                    'Nowa wizyta w kalendarzu',
                    "Dodano nową wizytę: {$appointment->title} w dniu " . $appointment->start_time->format('d.m.Y H:i'),
                    ['appointment_id' => $appointment->id],
                    $appointment
                );
            }
        } catch (\Exception $e) {
            Log::error('Błąd w appointmentCreated: ' . $e->getMessage());
        }
    }

    public static function appointmentUpdated($appointment)
    {
        if (!$appointment || !isset($appointment->id)) {
            Log::error('Nieprawidłowy obiekt appointment w appointmentUpdated');
            return;
        }

        try {

            if (isset($appointment->patient_id) && $appointment->patient_id) {
                self::createForUser(
                    $appointment->patient_id,
                    'appointment_updated',
                    'Zmiana wizyty',
                    "Wizyta {$appointment->title} została zaktualizowana na " . $appointment->start_time->format('d.m.Y H:i'),
                    ['appointment_id' => $appointment->id],
                    $appointment
                );
            }


            if (isset($appointment->doctor_id) && $appointment->doctor_id && Auth::check() && Auth::id() !== $appointment->doctor_id) {
                self::createForUser(
                    $appointment->doctor_id,
                    'appointment_updated',
                    'Wizyta została zmieniona',
                    "Wizyta {$appointment->title} została zaktualizowana na " . $appointment->start_time->format('d.m.Y H:i'),
                    ['appointment_id' => $appointment->id],
                    $appointment
                );
            }
        } catch (\Exception $e) {
            Log::error('Błąd w appointmentUpdated: ' . $e->getMessage());
        }
    }

    public static function appointmentCancelled($appointment, $reason = null)
    {
        if (!$appointment || !isset($appointment->id)) {
            Log::error('Nieprawidłowy obiekt appointment w appointmentCancelled');
            return;
        }

        try {
            $message = "Wizyta {$appointment->title} zaplanowana na " . $appointment->start_time->format('d.m.Y H:i') . " została anulowana.";

            if ($reason) {
                $message .= " Powód: {$reason}";
            }


            if (isset($appointment->patient_id) && $appointment->patient_id) {
                self::createForUser(
                    $appointment->patient_id,
                    'appointment_cancelled',
                    'Anulowanie wizyty',
                    $message,
                    ['appointment_id' => $appointment->id, 'reason' => $reason],
                    $appointment
                );
            }


            if (isset($appointment->doctor_id) && $appointment->doctor_id && Auth::check() && Auth::id() !== $appointment->doctor_id) {
                self::createForUser(
                    $appointment->doctor_id,
                    'appointment_cancelled',
                    'Anulowanie wizyty',
                    $message,
                    ['appointment_id' => $appointment->id, 'reason' => $reason],
                    $appointment
                );
            }
        } catch (\Exception $e) {
            Log::error('Błąd w appointmentCancelled: ' . $e->getMessage());
        }
    }

    public static function appointmentReminder($appointment, $minutesBefore = 60)
    {
        if (!$appointment || !isset($appointment->id)) {
            Log::error('Nieprawidłowy obiekt appointment w appointmentReminder');
            return;
        }

        try {
            $message = "Przypomnienie: Wizyta {$appointment->title} rozpocznie się za {$minutesBefore} minut (" . $appointment->start_time->format('H:i') . ").";


            if (isset($appointment->patient_id) && $appointment->patient_id) {
                self::createForUser(
                    $appointment->patient_id,
                    'appointment_reminder',
                    'Przypomnienie o wizycie',
                    $message,
                    ['appointment_id' => $appointment->id, 'minutes_before' => $minutesBefore],
                    $appointment
                );
            }

            // Powiadomienie dla doktora
            if (isset($appointment->doctor_id) && $appointment->doctor_id) {
                self::createForUser(
                    $appointment->doctor_id,
                    'appointment_reminder',
                    'Przypomnienie o wizycie',
                    $message,
                    ['appointment_id' => $appointment->id, 'minutes_before' => $minutesBefore],
                    $appointment
                );
            }
        } catch (\Exception $e) {
            Log::error('Błąd w appointmentReminder: ' . $e->getMessage());
        }
    }

    public static function documentCreated($document)
    {
        if (!$document || !isset($document->id)) {
            Log::error('Nieprawidłowy obiekt document w documentCreated');
            return;
        }

        try {

            if (isset($document->patient_id) && $document->patient_id) {
                self::createForUser(
                    $document->patient_id,
                    'document_created',
                    'Nowy dokument medyczny',
                    "Dodano nowy dokument: {$document->title}",
                    ['document_id' => $document->id],
                    $document
                );
            }


            if (isset($document->doctor_id) && $document->doctor_id && Auth::check() && Auth::id() !== $document->doctor_id) {
                self::createForUser(
                    $document->doctor_id,
                    'document_created',
                    'Nowy dokument medyczny',
                    "Pacjent ma nowy dokument: {$document->title}",
                    ['document_id' => $document->id],
                    $document
                );
            }
        } catch (\Exception $e) {
            Log::error('Błąd w documentCreated: ' . $e->getMessage());
        }
    }

    public static function messageReceived($message)
    {
        if (!$message || !isset($message->id) || !isset($message->receiver_id)) {
            Log::error('Nieprawidłowy obiekt message w messageReceived');
            return;
        }

        try {

            $senderName = 'nieznany nadawca';
            if (isset($message->sender) && $message->sender) {
                $senderName = $message->sender->name ?? $senderName;
            }


            self::createForUser(
                $message->receiver_id,
                'message_received',
                'Nowa wiadomość',
                "Otrzymałeś nową wiadomość od " . $senderName,
                ['message_id' => $message->id],
                $message
            );
        } catch (\Exception $e) {
            Log::error('Błąd w messageReceived: ' . $e->getMessage());
        }
    }

    public static function userRegistered($user)
    {
        if (!$user || !isset($user->id)) {
            Log::error('Nieprawidłowy obiekt user w userRegistered');
            return;
        }

        try {

            $adminIds = User::where('role', 'admin')->pluck('id')->toArray();

            if (!empty($adminIds)) {
                self::createForMultipleUsers(
                    $adminIds,
                    'user_registered',
                    'Nowy użytkownik',
                    "Nowy użytkownik {$user->name} ({$user->role}) zarejestrował się w systemie",
                    ['user_id' => $user->id],
                    $user
                );
            }
        } catch (\Exception $e) {
            Log::error('Błąd w userRegistered: ' . $e->getMessage());
        }
    }

    public static function systemNotification($userIds, $title, $message, $data = [])
    {
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        return self::createForMultipleUsers(
            $userIds,
            'system',
            $title,
            $message,
            $data
        );
    }
}

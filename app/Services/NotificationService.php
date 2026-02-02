<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Utwórz powiadomienie dla użytkownika (tylko w aplikacji)
     */
    public function createNotification($userId, $type, $title, $message, $data = [], $relatedModel = null)
    {
        try {
            $notification = Notification::createForUser($userId, $type, $title, $message, $data, $relatedModel);

            Log::info("Utworzono powiadomienie dla użytkownika {$userId}: {$title}");

            return $notification;
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia powiadomienia: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Utwórz powiadomienia dla wielu użytkowników (tylko w aplikacji)
     */
    public function createNotificationForMultipleUsers($userIds, $type, $title, $message, $data = [], $relatedModel = null)
    {
        try {
            Notification::createForMultipleUsers($userIds, $type, $title, $message, $data, $relatedModel);

            $userCount = is_array($userIds) ? count($userIds) : 1;
            Log::info("Utworzono powiadomienia dla {$userCount} użytkowników: {$title}");

            return true;
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia powiadomień: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Oznacz powiadomienia jako przeczytane
     */
    public function markAsRead($notificationIds, $userId)
    {
        try {
            $count = Notification::whereIn('id', $notificationIds)
                                ->where('user_id', $userId)
                                ->unread()
                                ->update([
                                    'is_read' => true,
                                    'read_at' => now()
                                ]);

            return $count;
        } catch (\Exception $e) {
            Log::error('Błąd podczas oznaczania powiadomień jako przeczytane: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Usuń stare powiadomienia
     */
    public function cleanupOldNotifications($daysOld = 30)
    {
        try {
            $count = Notification::where('created_at', '<', now()->subDays($daysOld))
                                ->where('is_read', true)
                                ->delete();

            Log::info("Usunięto {$count} starych powiadomień");
            return $count;
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania starych powiadomień: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Pobierz statystyki powiadomień
     */
    public function getNotificationStats($userId)
    {
        try {
            return [
                'total' => Notification::forUser($userId)->count(),
                'unread' => Notification::forUser($userId)->unread()->count(),
                'today' => Notification::forUser($userId)->whereDate('created_at', today())->count(),
                'this_week' => Notification::forUser($userId)->where('created_at', '>=', now()->startOfWeek())->count(),
                'by_type' => Notification::forUser($userId)
                                       ->selectRaw('type, count(*) as count')
                                       ->groupBy('type')
                                       ->pluck('count', 'type')
                                       ->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania statystyk: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Utwórz przypomnienia o wizytach
     */
    public function createAppointmentReminders($minutesBefore = 60)
    {
        try {
            $appointments = \App\Models\Appointment::where('start_time', '>=', now())
                                                  ->where('start_time', '<=', now()->addMinutes($minutesBefore))
                                                  ->where('status', 'confirmed')
                                                  ->get();

            $count = 0;
            foreach ($appointments as $appointment) {
                // Sprawdź czy przypomnienie już nie zostało wysłane
                $existingReminder = Notification::where('type', 'appointment_reminder')
                                                ->where('related_id', $appointment->id)
                                                ->where('related_type', get_class($appointment))
                                                ->where('created_at', '>=', now()->subHours(2))
                                                ->exists();

                if (!$existingReminder) {
                    Notification::appointmentReminder($appointment, $minutesBefore);
                    $count++;
                }
            }

            if ($count > 0) {
                Log::info("Utworzono {$count} przypomnień o wizytach");
            }

            return $count;
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia przypomnień: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Powiadomienia systemowe dla administratorów
     */
    public function notifyAdmins($title, $message, $data = [])
    {
        try {
            $adminIds = User::where('role', 'admin')->pluck('id')->toArray();

            if (!empty($adminIds)) {
                return $this->createNotificationForMultipleUsers($adminIds, 'system', $title, $message, $data);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Błąd podczas powiadamiania adminów: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Powiadomienia o nowych rejestracjach
     */
    public function notifyNewUserRegistration($user)
    {
        $title = 'Nowy użytkownik w systemie';
        $message = "Użytkownik {$user->name} ({$user->role}) zarejestrował się w systemie.";
        $data = ['user_id' => $user->id, 'user_role' => $user->role];

        return $this->notifyAdmins($title, $message, $data);
    }

    /**
     * Oznacz wszystkie powiadomienia użytkownika jako przeczytane
     */
    public function markAllAsReadForUser($userId)
    {
        try {
            $count = Notification::forUser($userId)
                                ->unread()
                                ->update([
                                    'is_read' => true,
                                    'read_at' => now()
                                ]);

            Log::info("Oznaczono {$count} powiadomień jako przeczytane dla użytkownika {$userId}");
            return $count;
        } catch (\Exception $e) {
            Log::error('Błąd podczas oznaczania wszystkich powiadomień jako przeczytane: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Wyczyść przeczytane powiadomienia użytkownika
     */
    public function clearReadForUser($userId)
    {
        try {
            $count = Notification::forUser($userId)
                                ->read()
                                ->delete();

            Log::info("Usunięto {$count} przeczytanych powiadomień dla użytkownika {$userId}");
            return $count;
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania przeczytanych powiadomień: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Pobierz najnowsze powiadomienia dla użytkownika
     */
    public function getRecentNotifications($userId, $limit = 10, $type = null)
    {
        try {
            $query = Notification::forUser($userId)
                                ->with('related')
                                ->latest();

            if ($type) {
                $query->byType($type);
            }

            return $query->limit($limit)->get();
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania powiadomień: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Sprawdź czy użytkownik ma nieprzeczytane powiadomienia
     */
    public function hasUnreadNotifications($userId)
    {
        try {
            return Notification::forUser($userId)->unread()->exists();
        } catch (\Exception $e) {
            Log::error('Błąd podczas sprawdzania nieprzeczytanych powiadomień: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Utwórz powiadomienie o nowym dokumencie
     */
    public function notifyNewDocument($document)
    {
        Notification::documentCreated($document);
    }

    /**
     * Utwórz powiadomienie o nowej wiadomości
     */
    public function notifyNewMessage($message)
    {
        Notification::messageReceived($message);
    }

    /**
     * Utwórz powiadomienie o nowej wizycie
     */
    public function notifyNewAppointment($appointment)
    {
        Notification::appointmentCreated($appointment);
    }

    /**
     * Utwórz powiadomienie o zmianie wizyty
     */
    public function notifyAppointmentUpdated($appointment)
    {
        Notification::appointmentUpdated($appointment);
    }

    /**
     * Utwórz powiadomienie o anulowaniu wizyty
     */
    public function notifyAppointmentCancelled($appointment, $reason = null)
    {
        Notification::appointmentCancelled($appointment, $reason);
    }
}

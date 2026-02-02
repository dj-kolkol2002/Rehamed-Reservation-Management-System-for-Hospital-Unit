<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Wyświetl wszystkie powiadomienia użytkownika
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Notification::forUser($user->id)
                           ->with('related')
                           ->latest();

        // Filtry
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $notifications = $query->paginate(20);

        // Statystyki
        $stats = [
            'total' => Notification::forUser($user->id)->count(),
            'unread' => Notification::forUser($user->id)->unread()->count(),
            'today' => Notification::forUser($user->id)->whereDate('created_at', today())->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats', 'request'));
    }

    /**
     * Pobierz powiadomienia dla AJAX (dropdown w navbarze)
     */
    public function getNotifications(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 10);

            $notifications = Notification::forUser($user->id)
                                       ->latest()
                                       ->limit($limit)
                                       ->get();

            $unreadCount = Notification::forUser($user->id)->unread()->count();

            $notificationsData = $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'formatted_time' => $notification->formatted_time,
                    'icon' => $notification->default_icon,
                    'color' => $notification->color,
                    'is_new' => $notification->is_new,
                    'action_url' => $this->getActionUrl($notification)
                ];
            });

            return response()->json([
                'success' => true,
                'notifications' => $notificationsData,
                'unread_count' => $unreadCount,
                'has_more' => Notification::forUser($user->id)->count() > $limit
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania powiadomień: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Błąd podczas pobierania powiadomień',
                'notifications' => [],
                'unread_count' => 0,
                'has_more' => false
            ], 500);
        }
    }

    /**
     * Pobierz liczbę nieprzeczytanych powiadomień
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            $count = Notification::forUser($user->id)->unread()->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania licznika: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'count' => 0
            ], 500);
        }
    }

    /**
     * Wyświetl szczegóły powiadomienia
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)->first();

            if (!$notification) {
                abort(404, 'Powiadomienie nie zostało znalezione.');
            }

            // Sprawdź uprawnienia
            if ($notification->user_id !== $user->id && !$this->isAdmin($user)) {
                abort(403, 'Brak uprawnień do przeglądania tego powiadomienia.');
            }

            // Oznacz jako przeczytane przy wyświetleniu
            if (!$notification->is_read && $notification->user_id === $user->id) {
                $notification->markAsRead();
            }

            return view('notifications.show', compact('notification'));

        } catch (\Exception $e) {
            Log::error('Błąd podczas wyświetlania powiadomienia: ' . $e->getMessage());
            abort(500, 'Błąd serwera');
        }
    }

    /**
     * Oznacz powiadomienie jako przeczytane
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Powiadomienie nie zostało znalezione'
                ], 404);
            }

            // Sprawdź czy powiadomienie należy do użytkownika
            if ($notification->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brak uprawnień'
                ], 403);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Powiadomienie zostało oznaczone jako przeczytane'
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas oznaczania jako przeczytane: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd'
            ], 500);
        }
    }

    /**
     * Oznacz wszystkie powiadomienia jako przeczytane
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();

            $count = Notification::forUser($user->id)
                               ->unread()
                               ->update([
                                   'is_read' => true,
                                   'read_at' => now()
                               ]);

            return response()->json([
                'success' => true,
                'message' => "Oznaczono {$count} powiadomień jako przeczytane",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas oznaczania wszystkich jako przeczytane: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd'
            ], 500);
        }
    }

    /**
     * Metoda update - aktualizuj powiadomienie
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Powiadomienie nie zostało znalezione'
                ], 404);
            }

            // Sprawdź czy powiadomienie należy do użytkownika lub czy user jest adminem
            if ($notification->user_id !== $user->id && !$this->isAdmin($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brak uprawnień'
                ], 403);
            }

            // Walidacja danych
            $validator = Validator::make($request->all(), [
                'is_read' => 'boolean',
                'title' => 'sometimes|string|max:255',
                'message' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nieprawidłowe dane',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Aktualizuj powiadomienie
            $updateData = $request->only(['title', 'message']);

            if ($request->has('is_read')) {
                $updateData['is_read'] = $request->boolean('is_read');
                $updateData['read_at'] = $request->boolean('is_read') ? now() : null;
            }

            $notification->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Powiadomienie zostało zaktualizowane'
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji powiadomienia: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd'
            ], 500);
        }
    }

    /**
     * Usuń powiadomienie
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $notification = Notification::where('id', $id)->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Powiadomienie nie zostało znalezione'
                ], 404);
            }

            // Sprawdź czy powiadomienie należy do użytkownika lub czy user jest adminem
            if ($notification->user_id !== $user->id && !$this->isAdmin($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brak uprawnień'
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Powiadomienie zostało usunięte'
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania powiadomienia: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd'
            ], 500);
        }
    }

    /**
     * Usuń wszystkie przeczytane powiadomienia
     */
    public function clearRead()
    {
        try {
            $user = Auth::user();

            $count = Notification::forUser($user->id)
                               ->read()
                               ->delete();

            return response()->json([
                'success' => true,
                'message' => "Usunięto {$count} przeczytanych powiadomień",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania przeczytanych: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd'
            ], 500);
        }
    }

    /**
     * Usuń wszystkie powiadomienia użytkownika
     */
    public function deleteAll()
    {
        try {
            $user = Auth::user();

            $count = Notification::forUser($user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => "Usunięto {$count} powiadomień",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania wszystkich powiadomień: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd'
            ], 500);
        }
    }

    /**
     * Pobierz URL akcji dla powiadomienia
     */
    private function getActionUrl($notification)
    {
        if (!$notification) {
            return route('dashboard');
        }

        if ($notification->action_url) {
            return $notification->action_url;
        }

        try {
            switch ($notification->type) {
                case 'appointment_created':
                case 'appointment_updated':
                case 'appointment_cancelled':
                case 'appointment_reminder':
                    if ($notification->related_id) {
                        return route('calendar.details', $notification->related_id);
                    }
                    return route('calendar.index');

                case 'document_created':
                case 'document_updated':
                    if ($notification->related_id) {
                        return route('medical-documents.show', $notification->related_id);
                    }
                    return route('medical-documents.index');

                case 'message_received':
                    if ($notification->related && method_exists($notification->related, 'conversation')) {
                        return route('chat.conversation', $notification->related->conversation);
                    }
                    return route('chat.index');

                case 'user_registered':
                    if ($this->isAdmin() && $notification->related_id) {
                        return route('admin.users.show', $notification->related_id);
                    }
                    return route('dashboard');

                default:
                    return route('dashboard');
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania URL akcji: ' . $e->getMessage());
            return route('dashboard');
        }
    }

    /**
     * Sprawdź czy użytkownik jest administratorem
     */
    private function isAdmin($user = null)
    {
        try {
            $user = $user ?? Auth::user();
            return $user && isset($user->role) && $user->role === 'admin';
        } catch (\Exception $e) {
            Log::error('Błąd podczas sprawdzania roli admin: ' . $e->getMessage());
            return false;
        }
    }
}

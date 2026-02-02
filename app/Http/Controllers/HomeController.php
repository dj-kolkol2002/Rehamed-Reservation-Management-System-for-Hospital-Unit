<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Appointment;
use App\Models\MedicalDocument;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user role.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Sprawdź czy to pierwsza wizyta użytkownika i wyślij powiadomienie powitalne
        $this->sendWelcomeNotificationIfNeeded($user);

        // Przekieruj do odpowiedniego dashboardu na podstawie roli
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    /**
     * Wyślij powiadomienie powitalne dla nowych użytkowników
     */
    private function sendWelcomeNotificationIfNeeded($user)
    {
        // Sprawdź czy użytkownik ma już jakiekolwiek powiadomienia
        $hasNotifications = Notification::forUser($user->id)->exists();

        // Jeśli nie ma powiadomień i konto zostało utworzone w ciągu ostatnich 24 godzin
        if (!$hasNotifications && $user->created_at->diffInHours(now()) <= 24) {
            $this->createWelcomeNotification($user);
        }
    }

    /**
     * Utwórz powiadomienie powitalne
     */
    private function createWelcomeNotification($user)
    {
        $welcomeMessages = [
            'admin' => [
                'title' => 'Witaj w panelu administratora!',
                'message' => 'Masz pełny dostęp do systemu zarządzania kliniką. Możesz zarządzać użytkownikami, przeglądać raporty i konfigurować system.',
                'data' => ['priority' => 'high', 'category' => 'welcome']
            ],
            'doctor' => [
                'title' => 'Witaj w systemie kliniki!',
                'message' => 'Jako fizjoterapeuta masz dostęp do kalendarza wizyt, dokumentacji medycznej i systemu komunikacji z pacjentami.',
                'data' => ['priority' => 'medium', 'category' => 'welcome']
            ],
            'user' => [
                'title' => 'Witaj w naszej klinice!',
                'message' => 'Możesz przeglądać swoje wizyty, dokumentację medyczną i komunikować się z fizjoterapeutami. Życzymy szybkiego powrotu do zdrowia!',
                'data' => ['priority' => 'normal', 'category' => 'welcome']
            ]
        ];

        $welcome = $welcomeMessages[$user->role] ?? $welcomeMessages['user'];

        Notification::createForUser(
            $user->id,
            'system',
            $welcome['title'],
            $welcome['message'],
            $welcome['data']
        );

        // Dodatkowo dla nowych pacjentów - dodaj powiadomienie z poradami
        if ($user->role === 'user') {
            Notification::createForUser(
                $user->id,
                'system',
                'Przydatne informacje',
                'Zapoznaj się z naszymi zasadami: punktualność na wizyty, anulowanie minimum 24h wcześniej, regularne wykonywanie ćwiczeń.',
                ['priority' => 'low', 'category' => 'tips']
            );
        }

        // Dla nowych doktorów - dodaj powiadomienie o funkcjach systemu
        if ($user->role === 'doctor') {
            Notification::createForUser(
                $user->id,
                'system',
                'Funkcje systemu',
                'Pamiętaj o regularnym aktualizowaniu dokumentacji pacjentów i wykorzystywaniu kalendarza do planowania wizyt.',
                ['priority' => 'low', 'category' => 'tips']
            );
        }
    }

    /**
     * Pokaż dashboard gościa (dla nieanalogowanych użytkowników)
     */
    public function guest()
    {
        return view('welcome');
    }

    /**
     * Pokaż statystyki dashboardu (API endpoint)
     */
    public function getDashboardStats()
    {
        $user = Auth::user();

        $stats = [
            'notifications' => [
                'unread' => Notification::forUser($user->id)->unread()->count(),
                'today' => Notification::forUser($user->id)->whereDate('created_at', today())->count(),
                'total' => Notification::forUser($user->id)->count()
            ]
        ];

        // Dodaj statystyki specyficzne dla roli
        switch ($user->role) {
            case 'admin':
                $stats['system'] = [
                    'total_users' => User::count(),
                    'active_users' => User::where('is_active', true)->count(),
                    'new_users_today' => User::whereDate('created_at', today())->count(),
                    'total_appointments' => Appointment::count(),
                    'appointments_today' => Appointment::whereDate('start_time', today())->count(),
                    'total_documents' => MedicalDocument::count(),
                ];
                break;

            case 'doctor':
                $stats['doctor'] = [
                    'my_appointments_today' => Appointment::where('doctor_id', $user->id)
                        ->whereDate('start_time', today())->count(),
                    'upcoming_appointments' => Appointment::where('doctor_id', $user->id)
                        ->where('start_time', '>', now())
                        ->where('status', 'scheduled')->count(),
                    'my_patients' => MedicalDocument::where('doctor_id', $user->id)
                        ->distinct('patient_id')->count(),
                    'documents_this_month' => MedicalDocument::where('doctor_id', $user->id)
                        ->whereMonth('created_at', now()->month)->count(),
                ];
                break;

            case 'user':
                $stats['patient'] = [
                    'upcoming_appointments' => Appointment::where('patient_id', $user->id)
                        ->where('start_time', '>', now())
                        ->where('status', 'scheduled')->count(),
                    'completed_appointments' => Appointment::where('patient_id', $user->id)
                        ->where('status', 'completed')->count(),
                    'my_documents' => MedicalDocument::where('patient_id', $user->id)
                        ->where('is_private', false)->count(),
                    'next_appointment' => Appointment::where('patient_id', $user->id)
                        ->where('start_time', '>', now())
                        ->where('status', 'scheduled')
                        ->orderBy('start_time')
                        ->first()
                ];
                break;
        }

        return response()->json($stats);
    }

    /**
     * Pokaż ostatnie aktywności użytkownika
     */
    public function getRecentActivity()
    {
        $user = Auth::user();
        $activities = [];

        // Ostatnie powiadomienia
        $recentNotifications = Notification::forUser($user->id)
            ->latest()
            ->limit(5)
            ->get();

        foreach ($recentNotifications as $notification) {
            $activities[] = [
                'type' => 'notification',
                'title' => $notification->title,
                'description' => $notification->message,
                'time' => $notification->created_at,
                'icon' => $notification->default_icon,
                'color' => $notification->color
            ];
        }

        // Ostatnie wizyty (w zależności od roli)
        if ($user->role === 'user') {
            $recentAppointments = Appointment::where('patient_id', $user->id)
                ->latest('start_time')
                ->limit(3)
                ->with('doctor')
                ->get();

            foreach ($recentAppointments as $appointment) {
                $activities[] = [
                    'type' => 'appointment',
                    'title' => $appointment->title,
                    'description' => 'Wizyta u ' . $appointment->doctor->full_name,
                    'time' => $appointment->start_time,
                    'icon' => 'fas fa-calendar-check',
                    'color' => $appointment->status === 'completed' ? 'green' : 'blue'
                ];
            }
        } elseif ($user->role === 'doctor') {
            $recentAppointments = Appointment::where('doctor_id', $user->id)
                ->latest('start_time')
                ->limit(3)
                ->with('patient')
                ->get();

            foreach ($recentAppointments as $appointment) {
                $activities[] = [
                    'type' => 'appointment',
                    'title' => $appointment->title,
                    'description' => 'Pacjent: ' . ($appointment->patient ? $appointment->patient->full_name : 'Nieprzypisany'),
                    'time' => $appointment->start_time,
                    'icon' => 'fas fa-user-md',
                    'color' => $appointment->status === 'completed' ? 'green' : 'blue'
                ];
            }
        }

        // Ostatnie dokumenty
        if ($user->role === 'user') {
            $recentDocuments = MedicalDocument::where('patient_id', $user->id)
                ->where('is_private', false)
                ->latest()
                ->limit(3)
                ->with('doctor')
                ->get();
        } elseif ($user->role === 'doctor') {
            $recentDocuments = MedicalDocument::where('doctor_id', $user->id)
                ->latest()
                ->limit(3)
                ->with('patient')
                ->get();
        } else {
            $recentDocuments = MedicalDocument::latest()
                ->limit(3)
                ->with(['patient', 'doctor'])
                ->get();
        }

        foreach ($recentDocuments as $document) {
            $activities[] = [
                'type' => 'document',
                'title' => $document->title,
                'description' => $document->type_display . ' - ' . $document->status_display,
                'time' => $document->created_at,
                'icon' => 'fas fa-file-medical',
                'color' => $document->status === 'completed' ? 'green' : 'yellow'
            ];
        }

        // Sortuj według czasu (najnowsze najpierw)
        usort($activities, function($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });

        // Ogranicz do 10 ostatnich aktywności
        $activities = array_slice($activities, 0, 10);

        // Formatuj czas
        foreach ($activities as &$activity) {
            $activity['formatted_time'] = $activity['time']->diffForHumans();
            $activity['time'] = $activity['time']->toISOString();
        }

        return response()->json(['activities' => $activities]);
    }

    /**
     * Sprawdź czy są ważne powiadomienia do wyświetlenia
     */
    public function checkImportantNotifications()
    {
        $user = Auth::user();

        $important = [];

        // Sprawdź nadchodzące wizyty w następnych 2 godzinach
        if ($user->role === 'user') {
            $upcomingAppointment = Appointment::where('patient_id', $user->id)
                ->where('status', 'scheduled')
                ->whereBetween('start_time', [now(), now()->addHours(2)])
                ->with('doctor')
                ->first();

            if ($upcomingAppointment) {
                $important[] = [
                    'type' => 'appointment_reminder',
                    'title' => 'Nadchodząca wizyta',
                    'message' => "Masz wizytę o {$upcomingAppointment->start_time->format('H:i')} u {$upcomingAppointment->doctor->full_name}",
                    'priority' => 'high',
                    'action_url' => route('calendar.index')
                ];
            }
        } elseif ($user->role === 'doctor') {
            $upcomingAppointments = Appointment::where('doctor_id', $user->id)
                ->where('status', 'scheduled')
                ->whereBetween('start_time', [now(), now()->addHours(1)])
                ->count();

            if ($upcomingAppointments > 0) {
                $important[] = [
                    'type' => 'appointment_reminder',
                    'title' => 'Nadchodzące wizyty',
                    'message' => "Masz {$upcomingAppointments} wizyt w następnej godzinie",
                    'priority' => 'high',
                    'action_url' => route('calendar.index')
                ];
            }
        }

        // Sprawdź nieprzeczytane powiadomienia o wysokim priorytecie
        $highPriorityNotifications = Notification::forUser($user->id)
            ->unread()
            ->where('data->priority', 'high')
            ->count();

        if ($highPriorityNotifications > 0) {
            $important[] = [
                'type' => 'high_priority_notifications',
                'title' => 'Ważne powiadomienia',
                'message' => "Masz {$highPriorityNotifications} ważnych nieprzeczytanych powiadomień",
                'priority' => 'high',
                'action_url' => route('notifications.index', ['status' => 'unread'])
            ];
        }

        return response()->json(['important' => $important]);
    }

    /**
     * Oznacz że użytkownik był aktywny (aktualizuj last_activity)
     */
    public function updateActivity()
    {
        $user = Auth::user();
        $user->update(['last_activity' => now()]);

        return response()->json(['success' => true]);
    }
}

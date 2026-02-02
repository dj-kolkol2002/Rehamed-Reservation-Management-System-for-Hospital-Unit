<?php
// app/Http/Controllers/UserDashboardController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalDocument;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:user');
    }

    public function index()
    {
        $user = Auth::user();

        // Statystyki pacjenta
        $stats = [
            'upcoming_appointments' => Appointment::where('patient_id', $user->id)
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')->count(),
            'completed_appointments' => Appointment::where('patient_id', $user->id)
                ->where('status', 'completed')->count(),
            'total_appointments' => Appointment::where('patient_id', $user->id)->count(),
            'cancelled_appointments' => Appointment::where('patient_id', $user->id)
                ->where('status', 'cancelled')->count(),
            'my_documents' => MedicalDocument::where('patient_id', $user->id)
                ->where('is_private', false)->count(),
            'recent_documents' => MedicalDocument::where('patient_id', $user->id)
                ->where('is_private', false)
                ->where('created_at', '>=', now()->subMonth())->count(),
            'unread_messages' => $user->unread_messages_count,
        ];

        // Nadchodzące wizyty
        $upcomingAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $user->id)
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Ostatnie ukończone wizyty
        $recentAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('start_time', 'desc')
            ->limit(5)
            ->get();

        // Najnowsze dokumenty medyczne
        $recentDocuments = MedicalDocument::with(['doctor'])
            ->where('patient_id', $user->id)
            ->where('is_private', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Następna wizyta
        $nextAppointment = Appointment::with(['doctor'])
            ->where('patient_id', $user->id)
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->first();

        // Statystyki wizyt według typu dla tego pacjenta
        $myAppointmentsByType = Appointment::where('patient_id', $user->id)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Postęp leczenia (ostatnie 6 miesięcy)
        $treatmentProgress = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Appointment::where('patient_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('start_time', $date->month)
                ->whereYear('start_time', $date->year)
                ->count();

            $treatmentProgress[] = [
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'count' => $count
            ];
        }

        // Lekarze z którymi pacjent miał wizyty
        $myDoctors = Appointment::with(['doctor'])
            ->where('patient_id', $user->id)
            ->whereNotNull('doctor_id')
            ->get()
            ->pluck('doctor')
            ->filter()
            ->unique('id');

        return view('user.dashboard', compact(
            'stats',
            'upcomingAppointments',
            'recentAppointments',
            'recentDocuments',
            'nextAppointment',
            'myAppointmentsByType',
            'treatmentProgress',
            'myDoctors'
        ));
    }
}

<?php
// app/Http/Controllers/DoctorDashboardController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function index()
    {
        $user = Auth::user();

        // Statystyki lekarza
        $stats = [
            'my_patients' => $this->getMyPatientsCount(),
            'appointments_today' => Appointment::where('doctor_id', $user->id)
                ->whereDate('start_time', today())->count(),
            'upcoming_appointments' => Appointment::where('doctor_id', $user->id)
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')->count(),
            'completed_this_month' => Appointment::where('doctor_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('start_time', now()->month)
                ->whereYear('start_time', now()->year)->count(),
            'my_documents' => MedicalDocument::where('doctor_id', $user->id)->count(),
            'documents_this_month' => MedicalDocument::where('doctor_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Dzisiejsze wizyty lekarza
        $todayAppointments = Appointment::with(['patient'])
            ->where('doctor_id', $user->id)
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->get();

        // Nadchodzące wizyty (następne 7 dni)
        $upcomingAppointments = Appointment::with(['patient'])
            ->where('doctor_id', $user->id)
            ->whereBetween('start_time', [now(), now()->addDays(7)])
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Ostatnio dodani pacjenci
        $recentPatients = User::where('role', 'user')
            ->where('created_at', '>=', now()->subMonth())
            ->latest()
            ->limit(5)
            ->get();

        // Statystyki wizyt według typu dla tego lekarza
        $myAppointmentsByType = Appointment::where('doctor_id', $user->id)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Najbliższa wizyta
        $nextAppointment = Appointment::with(['patient'])
            ->where('doctor_id', $user->id)
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->first();

        return view('doctor.dashboard', compact(
            'stats',
            'todayAppointments',
            'upcomingAppointments',
            'recentPatients',
            'myAppointmentsByType',
            'nextAppointment'
        ));
    }

    /**
     * Pobierz liczbę pacjentów tego lekarza
     */
    private function getMyPatientsCount()
    {
        return MedicalDocument::where('doctor_id', Auth::id())
            ->distinct()
            ->count('patient_id');
    }
}

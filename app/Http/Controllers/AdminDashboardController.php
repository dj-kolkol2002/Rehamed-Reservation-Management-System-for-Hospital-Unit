<?php
// app/Http/Controllers/AdminDashboardController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\MedicalDocument;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        // Podstawowe statystyki
        $stats = [
            'total_users' => User::count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_patients' => User::where('role', 'user')->count(),
            'active_users' => User::where('is_active', true)->count(),

            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::whereDate('start_time', today())->count(),
            'upcoming_appointments' => Appointment::where('start_time', '>', now())
                ->where('status', 'scheduled')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),

            'total_documents' => MedicalDocument::count(),
            'documents_this_month' => MedicalDocument::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),

            'total_messages' => Message::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
        ];

        // Najnowsi użytkownicy
        $recentUsers = User::latest()->limit(5)->get();

        // Dzisiejsze wizyty
        $todayAppointments = Appointment::with(['doctor', 'patient'])
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Statystyki wizyt według typu
        $appointmentsByType = Appointment::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Najpopularniejsi lekarze (według liczby wizyt)
        $popularDoctors = Appointment::with('doctor')
            ->selectRaw('doctor_id, count(*) as appointments_count')
            ->groupBy('doctor_id')
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'todayAppointments',
            'appointmentsByType',
            'popularDoctors'
        ));
    }
}

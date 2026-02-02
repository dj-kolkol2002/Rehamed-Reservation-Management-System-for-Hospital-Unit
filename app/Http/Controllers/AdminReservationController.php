<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Wyświetl panel zarządzania rezerwacjami dla admina
     */
    public function index()
    {
        return view('admin.reservations.index');
    }

    /**
     * Pobierz wszystkie oczekujące rezerwacje (API)
     */
    public function getPendingReservations(Request $request)
    {
        $query = Appointment::where('reservation_status', 'pending')
            ->whereNotNull('patient_id')
            ->with(['patient', 'doctor']);

        // Filtrowanie po fizjoterapeucie
        if ($request->filled('doctor_id') && $request->doctor_id !== 'all') {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filtrowanie po dacie
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('start_time')->get();

        // Przygotuj dane z nazwami
        $pendingData = $appointments->map(function ($apt) {
            return [
                'id' => $apt->id,
                'title' => $apt->title,
                'type' => $apt->type,
                'start_time' => $apt->start_time,
                'end_time' => $apt->end_time,
                'notes' => $apt->notes,
                'reservation_type' => $apt->reservation_type,
                'priority' => $apt->priority,
                'created_at' => $apt->created_at,
                'patient_name' => $apt->patient ? $apt->patient->full_name : 'Brak danych',
                'patient_email' => $apt->patient ? $apt->patient->email : null,
                'doctor_name' => $apt->doctor ? $apt->doctor->full_name : 'Nieprzypisany',
                'doctor_id' => $apt->doctor_id,
            ];
        });

        // Statystyki
        $confirmedToday = Appointment::where('reservation_status', 'confirmed')
            ->whereDate('confirmed_at', today())
            ->count();

        $rejectedToday = Appointment::where('reservation_status', 'rejected')
            ->whereDate('rejected_at', today())
            ->count();

        $upcomingConfirmed = Appointment::where('status', 'scheduled')
            ->where('reservation_status', 'confirmed')
            ->where('start_time', '>', now())
            ->count();

        $totalPending = Appointment::where('reservation_status', 'pending')->count();

        return response()->json([
            'success' => true,
            'pending' => $pendingData,
            'confirmed_today' => $confirmedToday,
            'rejected_today' => $rejectedToday,
            'upcoming' => $upcomingConfirmed,
            'total_pending' => $totalPending
        ]);
    }

    /**
     * Potwierdź rezerwację
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        if ($appointment->reservation_status !== 'pending') {
            return response()->json([
                'error' => 'Ta rezerwacja nie oczekuje na potwierdzenie'
            ], 409);
        }

        try {
            $appointment->confirm();

            // Powiadomienie dla pacjenta
            Notification::createForUser(
                $appointment->patient_id,
                'reservation_confirmed',
                'Rezerwacja potwierdzona',
                "Twoja wizyta w dniu " . $appointment->start_time->format('d.m.Y H:i') . " została potwierdzona przez administratora",
                ['appointment_id' => $appointment->id],
                $appointment
            );

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została potwierdzona',
                'appointment' => $appointment->fresh(['patient', 'doctor'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas potwierdzania rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Odrzuć rezerwację
     */
    public function reject(Request $request, Appointment $appointment)
    {
        if ($appointment->reservation_status !== 'pending') {
            return response()->json([
                'error' => 'Ta rezerwacja nie oczekuje na potwierdzenie'
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Podaj powód odrzucenia',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $appointment->reject($request->reason);

            // Zwolnij miejsce w slocie
            $slot = \App\Models\SlotAvailability::forDoctor($appointment->doctor_id)
                ->onDate($appointment->start_time)
                ->where('start_time', $appointment->start_time->format('H:i:s'))
                ->first();

            if ($slot) {
                $slot->decrementBookings();
            }

            // Powiadomienie dla pacjenta
            Notification::createForUser(
                $appointment->patient_id,
                'reservation_rejected',
                'Rezerwacja odrzucona',
                "Twój wniosek o wizytę w dniu " . $appointment->start_time->format('d.m.Y H:i') . " został odrzucony. Powód: " . $request->reason,
                ['appointment_id' => $appointment->id],
                $appointment
            );

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została odrzucona',
                'appointment' => $appointment->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas odrzucania rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Przypisz rezerwację do innego fizjoterapeuty
     */
    public function reassign(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        $newDoctor = User::find($request->doctor_id);
        if (!$newDoctor || $newDoctor->role !== 'doctor') {
            return response()->json([
                'error' => 'Wybrany użytkownik nie jest fizjoterapeutą'
            ], 400);
        }

        $oldDoctorName = $appointment->doctor ? $appointment->doctor->full_name : 'Nieprzypisany';

        try {
            $appointment->update([
                'doctor_id' => $newDoctor->id
            ]);

            // Powiadomienie dla nowego fizjoterapeuty
            Notification::createForUser(
                $newDoctor->id,
                'reservation_assigned',
                'Nowa rezerwacja przypisana',
                "Administrator przypisał Ci rezerwację pacjenta na dzień " . $appointment->start_time->format('d.m.Y H:i'),
                ['appointment_id' => $appointment->id],
                $appointment
            );

            // Powiadomienie dla pacjenta
            Notification::createForUser(
                $appointment->patient_id,
                'reservation_reassigned',
                'Zmiana fizjoterapeuty',
                "Twoja wizyta została przypisana do: " . $newDoctor->full_name,
                ['appointment_id' => $appointment->id],
                $appointment
            );

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została przypisana do ' . $newDoctor->full_name,
                'appointment' => $appointment->fresh(['patient', 'doctor'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas przypisywania rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobierz listę wszystkich rezerwacji (nie tylko pending)
     */
    public function getAllReservations(Request $request)
    {
        $query = Appointment::whereNotNull('patient_id')
            ->with(['patient', 'doctor']);

        // Filtrowanie po statusie rezerwacji
        if ($request->filled('reservation_status') && $request->reservation_status !== 'all') {
            $query->where('reservation_status', $request->reservation_status);
        }

        // Filtrowanie po fizjoterapeucie
        if ($request->filled('doctor_id') && $request->doctor_id !== 'all') {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filtrowanie po dacie
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('start_time', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'reservations' => $appointments
        ]);
    }

    /**
     * Pobierz listę fizjoterapeutów dla filtra
     */
    public function getDoctors()
    {
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->orderBy('firstname')
            ->get(['id', 'firstname', 'lastname']);

        return response()->json([
            'success' => true,
            'doctors' => $doctors->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->firstname . ' ' . $doc->lastname,
                    'full_name' => $doc->firstname . ' ' . $doc->lastname
                ];
            })
        ]);
    }
}

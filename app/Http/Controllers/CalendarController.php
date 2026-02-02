<?php
// app/Http/Controllers/CalendarController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\DoctorSchedule;
use App\Models\ClinicHours;
use App\Models\Notification;
use App\Models\Payment;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CalendarController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->middleware('auth');
        $this->availabilityService = $availabilityService;
    }

    /**
     * Wyświetl kalendarz
     */
    public function index()
    {
        $user = Auth::user();

        // Pobierz dodatkowe dane dla formularza
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        $patients = User::where('role', 'user')->where('is_active', true)->get();

        return view('calendar.index', [
            'userRole' => $user->role,
            'userId' => $user->id,
            'doctors' => $doctors,
            'patients' => $patients
        ]);
    }

    /**
     * Pobierz wydarzenia dla kalendarza (API endpoint)
     */
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $query = Appointment::with(['doctor', 'patient']);

        // Filtrowanie według uprawnień użytkownika
        if ($user->role === 'user') {
            // Pacjent widzi tylko swoje wizyty
            $query->forPatient($user->id);
        } elseif ($user->role === 'doctor') {
            // Doktor widzi swoje wizyty
            $query->forDoctor($user->id);
        }
        // Admin widzi wszystkie wizyty (bez dodatkowego filtrowania)

        // Filtrowanie po dacie jeśli podano
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);
            $query->betweenDates($start, $end);
        }

        $appointments = $query->orderBy('start_time')->get();

        // Przekształć do formatu FullCalendar
        $events = $appointments->map(function ($appointment) use ($user) {
            $event = $appointment->toFullCalendarEvent();

            // Ustaw tytuł tylko raz, w zależności od roli użytkownika
            $event['title'] = $this->getEventTitle($appointment, $user->role);

            return $event;
        });

        // Wymuś no-cache aby zawsze pobierać свежo dane
        return response()->json($events)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Utwórz nową wizytę
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Tylko doktorzy i administratorzy mogą tworzyć wizyty
        if ($user->role === 'user') {
            return response()->json([
                'error' => 'Nie masz uprawnień do tworzenia wizyt.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:fizjoterapia,konsultacja,masaz,neurorehabilitacja,kontrola',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'doctor_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0|max:999999.99'
        ], [
            'title.required' => 'Tytuł wizyty jest wymagany.',
            'type.required' => 'Typ terapii jest wymagany.',
            'type.in' => 'Wybierz prawidłowy typ terapii.',
            'start.required' => 'Data rozpoczęcia jest wymagana.',
            'start.date' => 'Podaj prawidłową datę rozpoczęcia.',
            'end.required' => 'Data zakończenia jest wymagana.',
            'end.date' => 'Podaj prawidłową datę zakończenia.',
            'end.after' => 'Data zakończenia musi być późniejsza niż rozpoczęcia.',
            'doctor_id.exists' => 'Wybrany fizjoterapeuta nie istnieje.',
            'patient_id.exists' => 'Wybrany pacjent nie istnieje.',
            'notes.max' => 'Notatki nie mogą przekraczać 1000 znaków.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być ujemna.',
            'price.max' => 'Cena jest zbyt wysoka.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Dane są nieprawidłowe.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ustaw doctor_id
        $doctorId = $request->doctor_id ?? $user->id;

        // Sprawdź czy użytkownik może przypisać wizytę do tego doktora
        if ($user->role === 'doctor' && $doctorId != $user->id) {
            return response()->json([
                'error' => 'Nie możesz tworzyć wizyt dla innych fizjoterapeutów.'
            ], 403);
        }

        // Sprawdź czy doktor istnieje i ma odpowiednią rolę
        $doctor = User::find($doctorId);
        if (!$doctor || $doctor->role !== 'doctor') {
            return response()->json([
                'error' => 'Wybrany fizjoterapeuta jest nieprawidłowy.'
            ], 422);
        }

        // Sprawdź czy pacjent ma odpowiednią rolę (jeśli podano)
        if ($request->patient_id) {
            $patient = User::find($request->patient_id);
            if (!$patient || $patient->role !== 'user') {
                return response()->json([
                    'error' => 'Wybrany pacjent jest nieprawidłowy.'
                ], 422);
            }
        }

        // Parse dates z formularza (Europe/Warsaw)
        // Mutator w Appointment konwertuje do UTC przed zapisem w bazie
        $startTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->start, 'Europe/Warsaw');
        $endTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->end, 'Europe/Warsaw');

        // Sprawdź czy harmonogram doktora jest aktywny tego dnia
        $dayOfWeek = $startTime->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_active) {
            return response()->json([
                'error' => 'Fizjoterapeuta nie pracuje w wybranym dniu.'
            ], 422);
        }

        // Sprawdź czy wizyta mieści się w godzinach pracy kliniki i fizjoterapeuty
        $hoursValidation = $this->validateAppointmentHours($startTime, $endTime, $doctorId);
        if (!$hoursValidation['valid']) {
            return response()->json([
                'error' => $hoursValidation['message']
            ], 422);
        }

        $appointmentData = [
            'title' => $request->title,
            'type' => $request->type,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'doctor_id' => $doctorId,
            'patient_id' => $request->patient_id,
            'notes' => $request->notes,
            'price' => $request->price,
            'status' => 'scheduled'
        ];

        // Sprawdź konflikty czasowe
        $tempAppointment = new Appointment($appointmentData);
        if ($tempAppointment->hasTimeConflict()) {
            return response()->json([
                'error' => 'W tym czasie już istnieje inna wizyta dla tego fizjoterapeuty.'
            ], 422);
        }

        // Sprawdź czy pacjent nie ma już wizyty w tym samym czasie u innego fizjoterapeuty
        if ($tempAppointment->hasPatientTimeConflict()) {
            return response()->json([
                'error' => 'Ten pacjent ma już wizytę w tym samym czasie u innego fizjoterapeuty.'
            ], 422);
        }

        try {
            $appointment = Appointment::create($appointmentData);
            $appointment->load(['doctor', 'patient']);

            // Jeśli wizyta ma cenę, automatycznie utwórz Payment
            if ($appointment->price && $appointment->price > 0 && $appointment->patient_id) {
                try {
                    Payment::create([
                        'appointment_id' => $appointment->id,
                        'user_id' => $appointment->patient_id,
                        'amount' => $appointment->price,
                        'currency' => 'PLN',
                        'status' => 'pending',
                        'payment_method' => 'stripe',
                        'description' => "Płatność za wizytę: {$appointment->title}",
                    ]);
                } catch (\Exception $paymentError) {
                    \Log::error('Payment creation failed', [
                        'appointment_id' => $appointment->id,
                        'error' => $paymentError->getMessage()
                    ]);
                }
            }

            // NOWE: Utwórz powiadomienia o nowej wizycie
            Notification::appointmentCreated($appointment);

            return response()->json([
                'message' => 'Wizyta została pomyślnie utworzona.',
                'appointment' => $appointment->toFullCalendarEvent()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas tworzenia wizyty: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zaktualizuj wizytę
     */
    public function update(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeEditedBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do edycji tej wizyty.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:fizjoterapia,konsultacja,masaz,neurorehabilitacja,kontrola',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'doctor_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'status' => 'nullable|in:scheduled,completed,cancelled,no_show'
        ], [
            'title.required' => 'Tytuł wizyty jest wymagany.',
            'type.required' => 'Typ terapii jest wymagany.',
            'type.in' => 'Wybierz prawidłowy typ terapii.',
            'start.required' => 'Data rozpoczęcia jest wymagana.',
            'end.required' => 'Data zakończenia jest wymagana.',
            'end.after' => 'Data zakończenia musi być późniejsza niż rozpoczęcia.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być ujemna.',
            'price.max' => 'Cena jest zbyt wysoka.',
            'status.in' => 'Wybierz prawidłowy status wizyty.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Dane są nieprawidłowe.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Przygotuj dane do aktualizacji
        // NAPRAWIONO: Parsuj w APP_TIMEZONE, konwertuj na UTC dla storage
        $updateStartTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->start, 'Europe/Warsaw')->setTimezone('UTC');
        $updateEndTime = Carbon::createFromFormat('Y-m-d\TH:i', $request->end, 'Europe/Warsaw')->setTimezone('UTC');

        // Ustal doctor_id (może się zmienić)
        $doctorIdForValidation = $appointment->doctor_id;
        if ($request->has('doctor_id') && $user->role === 'admin') {
            $doctor = User::find($request->doctor_id);
            if (!$doctor || $doctor->role !== 'doctor') {
                return response()->json([
                    'error' => 'Wybrany fizjoterapeuta jest nieprawidłowy.'
                ], 422);
            }
            $doctorIdForValidation = $request->doctor_id;
        }

        // NOWE: Sprawdź czy harmonogram doktora jest aktywny tego dnia
        $dayOfWeek = $updateStartTime->copy()->setTimezone('Europe/Warsaw')->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $doctorIdForValidation)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_active) {
            return response()->json([
                'error' => 'Fizjoterapeuta nie pracuje w wybranym dniu.'
            ], 422);
        }

        // Sprawdź czy wizyta mieści się w godzinach pracy kliniki i fizjoterapeuty
        $hoursValidation = $this->validateAppointmentHours(
            $updateStartTime->copy()->setTimezone('Europe/Warsaw'),
            $updateEndTime->copy()->setTimezone('Europe/Warsaw'),
            $doctorIdForValidation
        );
        if (!$hoursValidation['valid']) {
            return response()->json([
                'error' => $hoursValidation['message']
            ], 422);
        }

        $updateData = [
            'title' => $request->title,
            'type' => $request->type,
            'start_time' => $updateStartTime,
            'end_time' => $updateEndTime,
            'notes' => $request->notes,
            'price' => $request->price
        ];

        // Aktualizacja statusu (tylko admin i właściciel wizyty)
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        // Aktualizacja doctor_id (tylko admin)
        if ($request->has('doctor_id') && $user->role === 'admin') {
            $updateData['doctor_id'] = $request->doctor_id;
        }

        // Aktualizacja patient_id
        if ($request->has('patient_id')) {
            if ($request->patient_id) {
                $patient = User::find($request->patient_id);
                if (!$patient || $patient->role !== 'user') {
                    return response()->json([
                        'error' => 'Wybrany pacjent jest nieprawidłowy.'
                    ], 422);
                }
            }
            $updateData['patient_id'] = $request->patient_id;
        }

        // Sprawdź konflikty czasowe (wykluczając obecną wizytę)
        $tempAppointment = new Appointment($updateData);
        $tempAppointment->doctor_id = $updateData['doctor_id'] ?? $appointment->doctor_id;
        $tempAppointment->patient_id = $updateData['patient_id'] ?? $appointment->patient_id;

        if ($tempAppointment->hasTimeConflict($appointment->id)) {
            return response()->json([
                'error' => 'W tym czasie już istnieje inna wizyta dla tego fizjoterapeuty.'
            ], 422);
        }

        // Sprawdź czy pacjent nie ma już wizyty w tym samym czasie u innego fizjoterapeuty
        if ($tempAppointment->hasPatientTimeConflict($appointment->id)) {
            return response()->json([
                'error' => 'Ten pacjent ma już wizytę w tym samym czasie u innego fizjoterapeuty.'
            ], 422);
        }

        try {
            // Sprawdź czy cena lub pacjent się zmienili
            $priceChanged = $request->price != $appointment->price;
            $patientChanged = $request->has('patient_id') && $request->patient_id != $appointment->patient_id;

            $appointment->update($updateData);
            $appointment->load(['doctor', 'patient']);

            // Jeśli zmienił się pacjent, przenieś płatność
            if ($patientChanged) {
                if ($appointment->payment) {
                    $appointment->payment->update(['user_id' => $appointment->patient_id]);
                }
            }

            // Jeśli zmienił się cena, zaktualizuj Payment
            if ($priceChanged) {
                if ($appointment->price && $appointment->price > 0) {
                    // Jeśli nie ma Payment, utwórz go
                    if (!$appointment->payment && $appointment->patient_id) {
                        Payment::create([
                            'appointment_id' => $appointment->id,
                            'user_id' => $appointment->patient_id,
                            'amount' => $appointment->price,
                            'currency' => 'PLN',
                            'status' => 'pending',
                            'payment_method' => 'stripe',
                            'description' => "Płatność za wizytę: {$appointment->title}",
                        ]);
                    } else if ($appointment->payment && $appointment->payment->status === 'pending') {
                        // Jeśli Payment jest pending, zaktualizuj kwotę
                        $appointment->payment->update(['amount' => $appointment->price]);
                    }
                }
            }

            // NOWE: Utwórz powiadomienia o zmianie wizyty
            Notification::appointmentUpdated($appointment);

            return response()->json([
                'message' => 'Wizyta została pomyślnie zaktualizowana.',
                'appointment' => $appointment->toFullCalendarEvent()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas aktualizacji wizyty.'
            ], 500);
        }
    }

    /**
     * Usuń wizytę
     */
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeEditedBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do usunięcia tej wizyty.'
            ], 403);
        }

        try {
            // NOWE: Utwórz powiadomienia o anulowaniu wizyty przed usunięciem
            Notification::appointmentCancelled($appointment);

            $appointment->delete();

            return response()->json([
                'message' => 'Wizyta została pomyślnie usunięta.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas usuwania wizyty.'
            ], 500);
        }
    }

    /**
     * Anuluj wizytę
     */
    public function cancel(Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeCancelledBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do anulowania tej wizyty.'
            ], 403);
        }

        // Sprawdź czy można anulować
        if ($appointment->status === 'cancelled') {
            return response()->json([
                'error' => 'Ta wizyta jest już anulowana.'
            ], 422);
        }

        if ($appointment->status === 'completed') {
            return response()->json([
                'error' => 'Nie można anulować zakończonej wizyty.'
            ], 422);
        }

        try {
            $appointment->update(['status' => 'cancelled']);
            $appointment->load(['doctor', 'patient']);

            // NOWE: Utwórz powiadomienia o anulowaniu wizyty
            Notification::appointmentCancelled($appointment);

            return response()->json([
                'message' => 'Wizyta została anulowana.',
                'appointment' => $appointment->toFullCalendarEvent()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas anulowania wizyty.'
            ], 500);
        }
    }

    /**
     * Oznacz wizytę jako zakończoną
     */
    public function complete(Appointment $appointment)
    {
        $user = Auth::user();

        // Tylko doktor i admin mogą oznaczać jako zakończone
        if (!$appointment->canBeEditedBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do tej akcji.'
            ], 403);
        }

        if ($appointment->status === 'completed') {
            return response()->json([
                'error' => 'Ta wizyta jest już oznaczona jako zakończona.'
            ], 422);
        }

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'error' => 'Nie można oznaczyć anulowanej wizyty jako zakończonej.'
            ], 422);
        }

        try {
            $appointment->update(['status' => 'completed']);
            $appointment->load(['doctor', 'patient']);

            return response()->json([
                'message' => 'Wizyta została oznaczona jako zakończona.',
                'appointment' => $appointment->toFullCalendarEvent()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas aktualizacji wizyty.'
            ], 500);
        }
    }

    /**
     * Pobierz listę doktorów
     */
    public function getDoctors()
    {
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->select('id', 'firstname', 'lastname', 'email')
            ->get();

        return response()->json($doctors);
    }

    /**
     * Pobierz listę pacjentów
     */
    public function getPatients()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $patients = User::where('role', 'user')
                ->where('is_active', true)
                ->select('id', 'firstname', 'lastname', 'email')
                ->get();
        } elseif ($user->role === 'doctor') {
            // Doktor widzi wszystkich aktywnych pacjentów
            $patients = User::where('role', 'user')
                ->where('is_active', true)
                ->select('id', 'firstname', 'lastname', 'email')
                ->get();
        } else {
            return response()->json(['error' => 'Brak uprawnień'], 403);
        }

        return response()->json($patients);
    }

    /**
     * Pobierz szczegóły wizyty (API - JSON)
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeViewedBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do przeglądania tej wizyty.'
            ], 403);
        }

        $appointment->load(['doctor', 'patient']);

        return response()->json([
            'appointment' => $appointment->toFullCalendarEvent(),
            'can_edit' => $appointment->canBeEditedBy($user),
            'can_cancel' => $appointment->canBeCancelledBy($user)
        ]);
    }

    /**
     * Wyświetl stronę ze szczegółami wizyty (HTML view)
     */
    public function details(Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeViewedBy($user)) {
            abort(403, 'Nie masz uprawnień do przeglądania tej wizyty.');
        }

        $appointment->load(['doctor', 'patient', 'payment.invoice']);

        return view('calendar.details', [
            'appointment' => $appointment,
            'canEdit' => $appointment->canBeEditedBy($user),
            'canCancel' => $appointment->canBeCancelledBy($user)
        ]);
    }

    /**
     * Pobierz statystyki wizyt dla dashboardu
     */
    public function getStats()
    {
        $user = Auth::user();
        $query = Appointment::query();

        // Filtrowanie według roli
        if ($user->role === 'user') {
            $query->forPatient($user->id);
        } elseif ($user->role === 'doctor') {
            $query->forDoctor($user->id);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'today' => (clone $query)->today()->count(),
            'this_week' => (clone $query)->thisWeek()->count(),
            'this_month' => (clone $query)->thisMonth()->count(),
            'scheduled' => (clone $query)->scheduled()->count(),
            'completed' => (clone $query)->byStatus('completed')->count(),
            'cancelled' => (clone $query)->byStatus('cancelled')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Pobierz najbliższe wizyty
     */
    public function getUpcoming(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 5);

        $query = Appointment::with(['doctor', 'patient'])
            ->future()
            ->scheduled()
            ->orderBy('start_time');

        // Filtrowanie według roli
        if ($user->role === 'user') {
            $query->forPatient($user->id);
        } elseif ($user->role === 'doctor') {
            $query->forDoctor($user->id);
        }

        $appointments = $query->take($limit)->get();

        return response()->json([
            'appointments' => $appointments->map(function ($appointment) use ($user) {
                $event = $appointment->toFullCalendarEvent();
                $event['title'] = $this->getEventTitle($appointment, $user->role);
                return $event;
            })
        ]);
    }

    /**
     * Przesuń wizytę (drag & drop)
     */
    public function move(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeEditedBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do przenoszenia tej wizyty.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane czasowe.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Parse dates from frontend (sent as "2025-12-15T19:00" in Europe/Warsaw timezone)
        // Mutator w Appointment będzie konwertować do UTC dla bazy
        try {
            $newStartTime = Carbon::parse($request->start, 'Europe/Warsaw');
            $newEndTime = Carbon::parse($request->end, 'Europe/Warsaw');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nieprawidłowy format daty. Oczekiwano formatu: Y-m-d\TH:i',
                'debug' => $e->getMessage()
            ], 422);
        }

        // Sprawdź czy harmonogram doktora jest aktywny tego dnia
        $dayOfWeek = $newStartTime->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_active) {
            return response()->json([
                'error' => 'Fizjoterapeuta nie pracuje w wybranym dniu.'
            ], 422);
        }

        // Sprawdź czy wizyta mieści się w godzinach pracy kliniki i fizjoterapeuty
        $hoursValidation = $this->validateAppointmentHours($newStartTime, $newEndTime, $appointment->doctor_id);
        if (!$hoursValidation['valid']) {
            return response()->json([
                'error' => $hoursValidation['message']
            ], 422);
        }

        // Sprawdź konflikty czasowe
        $tempAppointment = new Appointment([
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
            'doctor_id' => $appointment->doctor_id
        ]);

        if ($tempAppointment->hasTimeConflict($appointment->id)) {
            return response()->json([
                'error' => 'W tym czasie już istnieje inna wizyta.'
            ], 422);
        }

        try {
            $appointment->update([
                'start_time' => $newStartTime,
                'end_time' => $newEndTime
            ]);

            $appointment->load(['doctor', 'patient']);

            // NOWE: Utwórz powiadomienia o zmianie wizyty
            Notification::appointmentUpdated($appointment);

            return response()->json([
                'message' => 'Wizyta została przeniesiona.',
                'appointment' => $appointment->toFullCalendarEvent()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas przenoszenia wizyty.'
            ], 500);
        }
    }

    /**
     * Zmień rozmiar wizyty (resize)
     */
    public function resize(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$appointment->canBeEditedBy($user)) {
            return response()->json([
                'error' => 'Nie masz uprawnień do zmiany tej wizyty.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'end' => 'required|date|after:' . $appointment->start_time->toDateTimeString()
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowy czas zakończenia.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Parse date from frontend (sent in Europe/Warsaw timezone)
        // Mutator w Appointment będzie konwertować do UTC dla bazy
        try {
            $newEndTime = Carbon::parse($request->end, 'Europe/Warsaw');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Nieprawidłowy format daty. Oczekiwano formatu: Y-m-d\TH:i',
                'debug' => $e->getMessage()
            ], 422);
        }

        // Sprawdź czy harmonogram doktora jest aktywny tego dnia
        $dayOfWeek = $appointment->start_time->copy()->setTimezone('Europe/Warsaw')->dayOfWeek;
        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_active) {
            return response()->json([
                'error' => 'Fizjoterapeuta nie pracuje w wybranym dniu.'
            ], 422);
        }

        // Sprawdź czy wizyta mieści się w godzinach pracy kliniki i fizjoterapeuty
        $hoursValidation = $this->validateAppointmentHours($appointment->start_time, $newEndTime, $appointment->doctor_id);
        if (!$hoursValidation['valid']) {
            return response()->json([
                'error' => $hoursValidation['message']
            ], 422);
        }

        // Sprawdź konflikty czasowe
        $tempAppointment = new Appointment([
            'start_time' => $appointment->start_time,
            'end_time' => $newEndTime,
            'doctor_id' => $appointment->doctor_id
        ]);

        if ($tempAppointment->hasTimeConflict($appointment->id)) {
            return response()->json([
                'error' => 'Nowy czas koliduje z inną wizytą.'
            ], 422);
        }

        try {
            $appointment->update(['end_time' => $newEndTime]);
            $appointment->load(['doctor', 'patient']);

            // NOWE: Utwórz powiadomienia o zmianie wizyty
            Notification::appointmentUpdated($appointment);

            return response()->json([
                'message' => 'Czas wizyty został zmieniony.',
                'appointment' => $appointment->toFullCalendarEvent()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wystąpił błąd podczas zmiany wizyty.'
            ], 500);
        }
    }

    // === PRIVATE HELPER METHODS ===

    /**
     * Sprawdź czy wizyta mieści się w godzinach pracy
     * Sprawdza zarówno godziny otwarcia kliniki (ClinicHours) jak i harmonogram fizjoterapeuty (DoctorSchedule)
     *
     * @param Carbon $startTime - Start time (w Europe/Warsaw timezone)
     * @param Carbon $endTime - End time (w Europe/Warsaw timezone)
     * @param int $doctorId - ID fizjoterapeuty
     * @return array ['valid' => bool, 'message' => string]
     */
    private function validateAppointmentHours($startTime, $endTime, $doctorId)
    {
        // Upewnij się że jest Europe/Warsaw timezone
        $start = Carbon::instance($startTime)->setTimezone('Europe/Warsaw');
        $end = Carbon::instance($endTime)->setTimezone('Europe/Warsaw');

        $dayOfWeek = $start->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $dayName = ClinicHours::DAY_NAMES[$dayOfWeek] ?? 'wybrany dzień';
        $requestedTime = $start->format('H:i') . '-' . $end->format('H:i');

        // Pobierz dane fizjoterapeuty dla komunikatów
        $doctor = User::find($doctorId);
        $doctorName = $doctor ? $doctor->firstname . ' ' . $doctor->lastname : 'Fizjoterapeuta';

        // 1. SPRAWDŹ GODZINY OTWARCIA KLINIKI (ClinicHours)
        $clinicHours = ClinicHours::where('day_of_week', $dayOfWeek)->first();

        if (!$clinicHours || !$clinicHours->is_active) {
            return [
                'valid' => false,
                'message' => "Nie można zaplanować wizyty - klinika jest zamknięta w {$dayName}. Sprawdź godziny otwarcia kliniki w ustawieniach."
            ];
        }

        // Sprawdź czy wizyta mieści się w godzinach otwarcia kliniki
        $clinicOpen = $clinicHours->start_time->format('H:i');
        $clinicClose = $clinicHours->end_time->format('H:i');

        if ($start->format('H:i') < $clinicOpen || $end->format('H:i') > $clinicClose) {
            return [
                'valid' => false,
                'message' => "Nie można zaplanować wizyty na {$requestedTime} - klinika jest otwarta tylko w godzinach {$clinicOpen}-{$clinicClose} ({$dayName}). Zmień godzinę wizyty lub dostosuj godziny otwarcia kliniki."
            ];
        }

        // 2. SPRAWDŹ HARMONOGRAM FIZJOTERAPEUTY (DoctorSchedule)
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_active) {
            return [
                'valid' => false,
                'message' => "Nie można zaplanować wizyty - {$doctorName} nie pracuje w {$dayName}. Wybierz inny dzień lub zmień harmonogram pracy fizjoterapeuty."
            ];
        }

        // Sprawdź czy wizyta mieści się w godzinach pracy fizjoterapeuty
        $scheduleStart = $schedule->start_time->format('H:i');
        $scheduleEnd = $schedule->end_time->format('H:i');

        if ($start->format('H:i') < $scheduleStart || $end->format('H:i') > $scheduleEnd) {
            return [
                'valid' => false,
                'message' => "Nie można zaplanować wizyty na {$requestedTime} - {$doctorName} pracuje tylko w godzinach {$scheduleStart}-{$scheduleEnd} ({$dayName}). Zmień godzinę wizyty lub dostosuj harmonogram pracy fizjoterapeuty."
            ];
        }

        // Sprawdź przerwę fizjoterapeuty (jeśli istnieje)
        if ($schedule->break_start && $schedule->break_end) {
            $breakStart = $schedule->break_start;
            $breakEnd = $schedule->break_end;

            // Sprawdź czy wizyta nie przecina się z przerwą
            $startMinutes = $start->hour * 60 + $start->minute;
            $endMinutes = $end->hour * 60 + $end->minute;
            $breakStartMinutes = $breakStart->hour * 60 + $breakStart->minute;
            $breakEndMinutes = $breakEnd->hour * 60 + $breakEnd->minute;

            if (!($endMinutes <= $breakStartMinutes || $startMinutes >= $breakEndMinutes)) {
                return [
                    'valid' => false,
                    'message' => "Nie można zaplanować wizyty na {$requestedTime} - {$doctorName} ma przerwę w godzinach {$breakStart->format('H:i')}-{$breakEnd->format('H:i')}. Wybierz inną godzinę."
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Pobierz tytuł wydarzenia w zależności od roli użytkownika
     */
    private function getEventTitle($appointment, $role)
    {
        $title = $appointment->title ?? 'Wizyta';

        switch($role) {
            case 'admin':
                $patientName = $appointment->patient ?
                    $appointment->patient->firstname . ' ' . $appointment->patient->lastname :
                    'Blokada czasu';
                $doctorName = $appointment->doctor ?
                    'Dr ' . $appointment->doctor->firstname . ' ' . $appointment->doctor->lastname :
                    'Nieznany doktor';
                return $title . ' - ' . $patientName . ' (' . $doctorName . ')';

            case 'doctor':
                $patientName = $appointment->patient ?
                    $appointment->patient->firstname . ' ' . $appointment->patient->lastname :
                    'Blokada czasu';
                return $title . ' - ' . $patientName;

            case 'user':
                $doctorName = $appointment->doctor ?
                    'Dr ' . $appointment->doctor->firstname . ' ' . $appointment->doctor->lastname :
                    'Nieznany doktor';
                return $title . ' - ' . $doctorName;

            default:
                return $title;
        }
    }

    /**
     * Pobierz dostępne sloty rezerwacji dla kalendarza
     */
    public function getAvailableSlotsForCalendar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        $doctor = User::find($request->doctor_id);
        if (!$doctor || $doctor->role !== 'doctor') {
            return response()->json([
                'error' => 'Doktor nie znaleziony'
            ], 404);
        }

        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        // Pobierz dostępne sloty
        $availableSlots = $this->availabilityService->getAvailableSlots($doctor, $start, $end, 60);

        // Konwertuj sloty do formatu FullCalendar jako events
        $events = [];
        foreach ($availableSlots as $date => $slots) {
            foreach ($slots as $slot) {
                // NAPRAWIONO: Wysyłaj z offsetem timezone'u, nie UTC
                // FullCalendar rozumie format z offsetem (+01:00)
                // NIE używaj setTimezone('UTC') bo to wysyła Z i FullCalendar nie konwertuje!
                $events[] = [
                    'id' => 'available-' . $slot['start']->timestamp,
                    'title' => '⊙ Dostępny termin',
                    'start' => $slot['start']->toIso8601String(),
                    'end' => $slot['end']->toIso8601String(),
                    'backgroundColor' => '#10b98150', // Semi-transparent green
                    'borderColor' => '#10b981',
                    'textColor' => '#059669',
                    'editable' => false,
                    'selectable' => true,
                    'extendedProps' => [
                        'type' => 'availability',
                        'time' => $slot['time']
                    ]
                ];
            }
        }

        return response()->json([
            'success' => true,
            'events' => $events,
            'count' => count($events)
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Pobierz harmonogram doktora
     */
    public function getDoctorSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Doktor nie znaleziony'
            ], 404);
        }

        $doctor = User::find($request->doctor_id);
        if (!$doctor || $doctor->role !== 'doctor') {
            return response()->json([
                'error' => 'Doktor nie znaleziony'
            ], 404);
        }

        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $schedule->day_name,
                    'start_time' => $schedule->start_time->format('H:i'),
                    'end_time' => $schedule->end_time->format('H:i'),
                    'break_start' => $schedule->break_start ? $schedule->break_start->format('H:i') : null,
                    'break_end' => $schedule->break_end ? $schedule->break_end->format('H:i') : null,
                    'appointment_duration' => $schedule->appointment_duration,
                    'is_active' => $schedule->is_active
                ];
            });

        return response()->json([
            'success' => true,
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->firstname . ' ' . $doctor->lastname,
            'schedules' => $schedules
        ]);
    }
}

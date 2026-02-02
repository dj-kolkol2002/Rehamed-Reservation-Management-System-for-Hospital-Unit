<?php
// app/Http/Controllers/ReservationController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\DoctorSchedule;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReservationController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->middleware('auth');
        $this->availabilityService = $availabilityService;
    }

    /**
     * Pokaż stronę rezerwacji
     */
    public function index()
    {
        // Dla wszystkich zalogowanych - pokaż formularz rezerwacji
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->with('doctorSchedules')
            ->get();

        return view('reservation.index', [
            'doctors' => $doctors
        ]);
    }

    /**
     * Pokaż listę rezerwacji (dla doktorów i adminów)
     */
    public function myReservations(Request $request)
    {
        $user = Auth::user();

        // Tylko doktorzy i adminowie mogą widzieć listę rezerwacji
        if (!$user->isDoctor() && !$user->isAdmin()) {
            abort(403, 'Brak dostępu');
        }

        // Budowanie zapytania
        $query = Appointment::query();

        if ($user->isDoctor()) {
            // Doktor widzi tylko swoje rezerwacje (wizyty które pacjenci zarezerwowali u niego)
            $query->where('doctor_id', $user->id);
        }

        // Tylko rezerwacje pacjentów
        $query->whereNotNull('patient_id');

        // Filtrowanie po statusie
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtrowanie po typie
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filtrowanie po dacie
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        // Filtrowanie po pacjencie (tylko dla admina)
        if ($user->isAdmin() && $request->filled('patient_id') && $request->patient_id !== 'all') {
            $query->where('patient_id', $request->patient_id);
        }

        // Filtrowanie po lekarzu (tylko dla admina)
        if ($user->isAdmin() && $request->filled('doctor_id') && $request->doctor_id !== 'all') {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Wyszukiwanie po tytule
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sortowanie
        $sortBy = $request->get('sort_by', 'start_time');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $appointments = $query->with(['patient', 'doctor'])->paginate(15);

        // Pobierz wszystkich pacjentów i lekarzy dla filtrów (tylko dla admina)
        $patients = $user->isAdmin() ? User::where('role', 'user')->orderBy('firstname')->get() : collect();
        $doctors = $user->isAdmin() ? User::where('role', 'doctor')->orderBy('firstname')->get() : collect();

        return view('reservation.list', [
            'appointments' => $appointments,
            'isDoctor' => $user->isDoctor(),
            'isAdmin' => $user->isAdmin(),
            'patients' => $patients,
            'doctors' => $doctors,
            'filters' => $request->all()
        ]);
    }

    /**
     * Pobierz dostępne sloty dla doktora (API endpoint)
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'nullable|integer|min:15|max:480'
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

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $duration = $request->duration ?? 60;

        $availableSlots = $this->availabilityService->getAvailableSlots(
            $doctor,
            $startDate,
            $endDate,
            $duration
        );

        return response()->json([
            'success' => true,
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->firstname . ' ' . $doctor->lastname,
            'slots' => $availableSlots,
            'slots_count' => array_sum(array_map('count', $availableSlots))
        ]);
    }

    /**
     * Pobierz harmonogram pracy doktora
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

        $workingDays = $this->availabilityService->getWorkingDays($doctor);

        return response()->json([
            'success' => true,
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->firstname . ' ' . $doctor->lastname,
            'working_days' => $workingDays
        ]);
    }

    /**
     * Utwórz rezerwację
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|in:fizjoterapia,konsultacja,masaz,neurorehabilitacja,kontrola',
            'notes' => 'nullable|string|max:500'
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

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        // Sprawdź czy termin jest dostępny
        if (!$this->availabilityService->canBookSlot($doctor, $startTime, $endTime)) {
            return response()->json([
                'error' => 'Wybrany termin jest niedostępny. Spróbuj wybrać inny czas.'
            ], 409);
        }

        // Sprawdź czy pacjent nie ma już wizyty w tym samym czasie
        $tempAppointment = new Appointment([
            'patient_id' => $user->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
        if ($tempAppointment->hasPatientTimeConflict()) {
            return response()->json([
                'error' => 'Masz już wizytę w tym samym czasie. Wybierz inny termin.'
            ], 409);
        }

        try {
            $appointment = Appointment::create([
                'title' => $request->title,
                'type' => $request->type,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'doctor_id' => $doctor->id,
                'patient_id' => $user->id,
                'notes' => $request->notes,
                'status' => 'scheduled',
                'color' => null // Będzie ustawiony z domyślnych kolorów w modelu
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została pomyślnie utworzona',
                'appointment' => $appointment->load(['doctor', 'patient'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas tworzenia rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pokaż szczegóły rezerwacji
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik może zobaczyć rezerwację
        // Dla wizyt bez przypisanego lekarza - sprawdź czy użytkownik jest na liście dostępnych
        $canView = $appointment->canBeViewedBy($user);

        if (!$canView && $appointment->doctor_id === null) {
            $metadata = $appointment->metadata ?? [];
            $availableDoctorIds = $metadata['available_doctor_ids'] ?? [];
            if ($user->isDoctor() && in_array($user->id, $availableDoctorIds)) {
                $canView = true;
            }
        }

        if (!$canView) {
            abort(403, 'Brak dostępu do tej rezerwacji');
        }

        $appointment->load(['doctor', 'patient', 'payment']);

        // Pobierz listę dostępnych lekarzy jeśli wizyta nie ma przypisanego
        $availableDoctors = collect();
        if ($appointment->doctor_id === null) {
            $metadata = $appointment->metadata ?? [];
            $availableDoctorIds = $metadata['available_doctor_ids'] ?? [];
            if (!empty($availableDoctorIds)) {
                $availableDoctors = User::whereIn('id', $availableDoctorIds)->get();
            }
        }

        return view('reservation.show', [
            'appointment' => $appointment,
            'availableDoctors' => $availableDoctors
        ]);
    }

    /**
     * Edytuj rezerwację
     */
    public function update(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik może edytować
        if (!$appointment->canBeEditedBy($user)) {
            return response()->json([
                'error' => 'Brak dostępu do edycji tej rezerwacji'
            ], 403);
        }

        // Nie można edytować zakończonych wizyt
        if ($appointment->status === 'completed') {
            return response()->json([
                'error' => 'Nie można edytować już zakończonych wizyt'
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        // Sprawdź czy nowy termin jest dostępny
        if (!$this->availabilityService->canBookSlot($appointment->doctor, $startTime, $endTime)) {
            return response()->json([
                'error' => 'Wybrany termin jest niedostępny'
            ], 409);
        }

        try {
            $updateData = [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'notes' => $request->notes
            ];

            if ($request->has('title')) {
                $updateData['title'] = $request->title;
            }

            $appointment->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została zmieniona',
                'appointment' => $appointment->load(['doctor', 'patient'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas aktualizacji rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Anuluj rezerwację
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if (!$appointment->canBeCancelledBy($user)) {
            return response()->json([
                'error' => 'Nie możesz anulować tę rezerwację'
            ], 403);
        }

        try {
            $appointment->update([
                'status' => 'cancelled'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została anulowana',
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas anulowania rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobierz dostępne dni dla doktora
     */
    public function getAvailableDays(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane'
            ], 422);
        }

        $doctor = User::find($request->doctor_id);
        if (!$doctor || $doctor->role !== 'doctor') {
            return response()->json([
                'error' => 'Doktor nie znaleziony'
            ], 404);
        }

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->clone()->endOfMonth();

        // Pobierz harmonogram pracy
        $workingDays = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('is_active', true)
            ->pluck('day_of_week')
            ->toArray();

        // Znajdź dni w miesiącu gdy doktor pracuje
        $availableDays = [];
        $currentDate = $startDate->clone();

        while ($currentDate->lte($endDate)) {
            if (in_array($currentDate->dayOfWeek, $workingDays)) {
                // Sprawdź czy są dostępne sloty
                $slots = $this->availabilityService->getAvailableSlotsForDay($doctor, $currentDate);
                if (!empty($slots)) {
                    $availableDays[] = [
                        'date' => $currentDate->format('Y-m-d'),
                        'day_of_week' => $currentDate->format('l'),
                        'slots_count' => count($slots)
                    ];
                }
            }
            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->firstname . ' ' . $doctor->lastname,
            'available_days' => $availableDays,
            'month' => $month,
            'year' => $year
        ]);
    }

    /**
     * Wyślij wiadomość do doktora
     */
    public function sendMessage(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik to pacjent
        if ($appointment->patient_id !== $user->id) {
            return response()->json([
                'error' => 'Brak dostępu'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            // Tutaj można dodać logikę wysyłania wiadomości
            // Na razie zapisujemy wiadomość w notatce rezerwacji
            $existingNotes = $appointment->notes ?? '';
            $timestamp = now()->format('Y-m-d H:i:s');
            $appointment->update([
                'notes' => $existingNotes . "\n\n[Wiadomość od pacjenta - $timestamp]\n" . $request->message
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wiadomość została wysłana do doktora'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas wysyłania wiadomości',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zapisz notatki doktora
     */
    public function saveDoctorNotes(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik to doktor przypisany do wizyty
        if ($appointment->doctor_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'error' => 'Brak dostępu'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'doctor_notes' => 'required|string|min:1|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            // Sprawdź czy appointment istnieje
            if (!$appointment) {
                return response()->json([
                    'error' => 'Wizyta nie została znaleziona'
                ], 404);
            }

            // Zapisz notatki w metadanych
            $metadata = $appointment->metadata ?? [];
            $metadata['doctor_notes'] = $request->doctor_notes;
            $metadata['doctor_notes_updated_at'] = now()->format('Y-m-d H:i:s');

            $appointment->update([
                'metadata' => $metadata
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notatki doktora zostały zapisane'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas zapisywania notatek',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Wyślij wiadomość do pacjenta
     */
    public function sendPatientMessage(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik to doktor przypisany do wizyty
        if ($appointment->doctor_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'error' => 'Brak dostępu'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            // Sprawdź czy użytkownik istnieje i ma atrybut full_name
            if (!$user || !method_exists($user, 'getFullNameAttribute') && !isset($user->full_name)) {
                return response()->json([
                    'error' => 'Błąd użytkownika'
                ], 500);
            }

            // Tutaj można dodać logikę wysyłania wiadomości
            // Na razie zapisujemy wiadomość w metadanych
            $metadata = $appointment->metadata ?? [];
            if (!isset($metadata['patient_messages'])) {
                $metadata['patient_messages'] = [];
            }

            $senderName = $user->full_name ?? ($user->firstname . ' ' . $user->lastname);

            $metadata['patient_messages'][] = [
                'message' => $request->message,
                'sent_at' => now()->format('Y-m-d H:i:s'),
                'sender_name' => $senderName
            ];

            $appointment->update([
                'metadata' => $metadata
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wiadomość została wysłana do pacjenta'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas wysyłania wiadomości',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ustaw przypomnienie
     */
    public function setReminder(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik to pacjent
        if ($appointment->patient_id !== $user->id) {
            return response()->json([
                'error' => 'Brak dostępu'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'minutes' => 'required|integer|min:1|max:10080'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            // Zapisz przypomnienie w metadanych
            $metadata = $appointment->metadata ?? [];
            $metadata['reminder_minutes'] = $request->minutes;
            $metadata['reminder_set_at'] = now()->format('Y-m-d H:i:s');

            $appointment->update([
                'metadata' => $metadata
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Przypomnienie zostało ustawione'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas ustawiania przypomnienia',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Oznacz wizytę jako ukończoną
     */
    public function complete(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik to doktor przypisany do wizyty
        if ($appointment->doctor_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'error' => 'Brak dostępu'
            ], 403);
        }

        try {
            $appointment->update([
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wizyta została oznaczona jako ukończona',
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas aktualizacji wizyty',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * NOWY SYSTEM REZERWACJI
     * ========================================
     */

    /**
     * Pobierz dostępne sloty dla pacjenta (nowa metoda)
     * Teraz pobiera sloty od WSZYSTKICH aktywnych fizjoterapeutów
     */
    public function getPatientAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $patientId = $user->isPatient() ? $user->id : null;

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Pobierz sloty od wszystkich aktywnych fizjoterapeutów
        $slots = $this->availabilityService->getAvailableSlotsForAllDoctors(
            $startDate,
            $endDate,
            $patientId
        );

        return response()->json([
            'success' => true,
            'dates' => $slots,
            'total_slots' => array_sum(array_map('count', $slots))
        ]);
    }

    /**
     * Pobierz sugerowany (pierwszy dostępny) termin
     */
    public function getSuggestedSlot(Request $request)
    {
        $user = Auth::user();
        $patientId = $user->isPatient() ? $user->id : null;

        $slot = $this->availabilityService->getNextAvailableSlotForAnyDoctor($patientId);

        if (!$slot) {
            return response()->json([
                'success' => false,
                'message' => 'Brak dostępnych terminów'
            ]);
        }

        return response()->json([
            'success' => true,
            'slot' => $slot
        ]);
    }

    /**
     * Utwórz wniosek o rezerwację (pacjent)
     * Wysyła powiadomienie do WSZYSTKICH dostępnych fizjoterapeutów
     * Pierwszy który potwierdzi - przejmuje wizytę
     */
    public function createReservationRequest(Request $request)
    {
        $user = Auth::user();

        if (!$user->isPatient()) {
            return response()->json([
                'error' => 'Tylko pacjenci mogą składać wnioski o rezerwację'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'slot_id' => 'required|string', // Format: slot_X lub suggested_doctorId_datetime
            'title' => 'required|string|max:255',
            'type' => 'required|in:fizjoterapia,konsultacja,masaz,neurorehabilitacja,kontrola',
            'notes' => 'nullable|string|max:1000',
            'reservation_type' => 'required|in:online,phone,in_person'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        // Parsuj slot_id aby wyciągnąć datę i czas
        $slotData = $request->input('slot_data');

        if (!$slotData) {
            return response()->json([
                'error' => 'Brak danych slotu'
            ], 422);
        }

        $date = $slotData['date'] ?? null;
        $startTime = $slotData['start_time'] ?? null;
        $endTime = $slotData['end_time'] ?? null;
        $doctorIds = $slotData['doctor_ids'] ?? [];

        if (!$date || !$startTime || !$endTime || empty($doctorIds)) {
            return response()->json([
                'error' => 'Niepełne dane slotu'
            ], 422);
        }

        // Upewnij się że doctorIds to tablica
        $availableDoctorIds = is_array($doctorIds) ? $doctorIds : [$doctorIds];

        // Sprawdź czy wszyscy lekarze są aktywni
        $activeDoctors = User::whereIn('id', $availableDoctorIds)
            ->where('role', 'doctor')
            ->where('is_active', true)
            ->get();

        if ($activeDoctors->isEmpty()) {
            return response()->json([
                'error' => 'Brak dostępnych fizjoterapeutów w tym terminie'
            ], 409);
        }

        // Utwórz Carbon dla czasu - WAŻNE: parsuj z jawnym timezone aby uniknąć błędów
        // gdy timezone serwera różni się od Europe/Warsaw
        $timezone = config('app.timezone', 'Europe/Warsaw');
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $startTime, $timezone);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $endTime, $timezone);

        // Sprawdź czy slot jest dostępny (dla jakiegokolwiek lekarza)
        $slotAvailable = false;
        foreach ($activeDoctors as $doctor) {
            if ($this->availabilityService->isSlotFree($startDateTime, $endDateTime, null, $doctor->id)) {
                $slotAvailable = true;
                break;
            }
        }

        if (!$slotAvailable) {
            return response()->json([
                'error' => 'Ten termin jest już zajęty'
            ], 409);
        }

        try {
            // Utwórz wizytę BEZ przypisanego lekarza (doctor_id = null)
            // Dostępni lekarze zapisani w metadata
            $appointment = Appointment::create([
                'title' => $request->title,
                'type' => $request->type,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'doctor_id' => null, // NIE PRZYPISUJEMY - czeka na potwierdzenie
                'patient_id' => $user->id,
                'notes' => $request->notes,
                'status' => 'scheduled',
                'reservation_type' => $request->reservation_type,
                'reservation_status' => 'pending', // Wymaga potwierdzenia przez fizjoterapeutę
                'priority' => 'normal',
                'patient_can_cancel' => true,
                'metadata' => [
                    'available_doctor_ids' => $activeDoctors->pluck('id')->toArray(),
                    'created_at_slot' => $date . ' ' . $startTime . '-' . $endTime,
                ]
            ]);

            // Wyślij powiadomienie do WSZYSTKICH dostępnych fizjoterapeutów
            foreach ($activeDoctors as $doctor) {
                \App\Models\Notification::createForUser(
                    $doctor->id,
                    'reservation_request',
                    'Nowy wniosek o rezerwację - wymagane potwierdzenie',
                    "Pacjent {$user->full_name} prosi o wizytę ({$request->type}) w dniu " .
                        $startDateTime->format('d.m.Y') . " o " . $startDateTime->format('H:i') .
                        ". Kliknij aby potwierdzić i przejąć wizytę.",
                    ['appointment_id' => $appointment->id],
                    $appointment
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Wniosek o rezerwację został wysłany do ' . $activeDoctors->count() . ' fizjoterapeutów. Czeka na potwierdzenie.',
                'appointment' => $appointment->load(['patient'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas tworzenia wniosku',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Potwierdź wniosek o rezerwację (lekarz)
     * Fizjoterapeuta który potwierdzi jako pierwszy - przejmuje wizytę
     * Admin może przypisać wizytę do wybranego lub pierwszego dostępnego fizjoterapeuty
     */
    public function confirmReservation(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if (!$user->isDoctor() && !$user->isAdmin()) {
            return response()->json([
                'error' => 'Brak uprawnień'
            ], 403);
        }

        // Sprawdź czy wizyta oczekuje na potwierdzenie
        if (!$appointment->isPending()) {
            return response()->json([
                'error' => 'Ta wizyta została już przejęta przez innego fizjoterapeutę.',
                'error_type' => 'already_taken'
            ], 409);
        }

        // Sprawdź uprawnienia do potwierdzenia
        $canConfirm = false;
        $metadata = $appointment->metadata ?? [];
        $availableDoctorIds = $metadata['available_doctor_ids'] ?? [];

        if ($user->isAdmin()) {
            // Admin może potwierdzić każdą wizytę
            $canConfirm = true;
        } elseif ($appointment->doctor_id === null) {
            // Wizyta bez przypisanego lekarza - sprawdź czy fizjoterapeuta jest na liście dostępnych
            $canConfirm = in_array($user->id, $availableDoctorIds);
        } elseif ($appointment->doctor_id === $user->id) {
            // Stary system - wizyta przypisana do konkretnego lekarza
            $canConfirm = true;
        }

        if (!$canConfirm) {
            return response()->json([
                'error' => 'Nie masz uprawnień do potwierdzenia tej wizyty'
            ], 403);
        }

        // Sprawdź czy pacjent nie ma już innej wizyty w tym samym czasie
        if ($appointment->patient_id && $appointment->hasPatientTimeConflict($appointment->id)) {
            return response()->json([
                'error' => 'Pacjent ma już wizytę w tym samym czasie u innego fizjoterapeuty.',
                'error_type' => 'patient_conflict'
            ], 409);
        }

        // Ustal który fizjoterapeuta będzie przypisany
        $assignedDoctorId = $appointment->doctor_id;
        if ($assignedDoctorId === null) {
            if ($user->isDoctor()) {
                $assignedDoctorId = $user->id;
            } elseif ($user->isAdmin() && $request->input('doctor_id')) {
                $assignedDoctorId = $request->input('doctor_id');
            } elseif ($user->isAdmin() && !empty($availableDoctorIds)) {
                $assignedDoctorId = $availableDoctorIds[0];
            }
        }

        // Sprawdź czy fizjoterapeuta nie ma już wizyty w tym samym czasie
        if ($assignedDoctorId) {
            $tempAppointment = new Appointment([
                'doctor_id' => $assignedDoctorId,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
            ]);
            if ($tempAppointment->hasTimeConflict($appointment->id)) {
                $doctorName = \App\Models\User::find($assignedDoctorId)?->full_name ?? 'Fizjoterapeuta';
                return response()->json([
                    'error' => "{$doctorName} ma już wizytę w tym samym czasie.",
                    'error_type' => 'doctor_conflict'
                ], 409);
            }
        }

        try {
            // Jeśli wizyta nie ma przypisanego lekarza
            if ($appointment->doctor_id === null) {
                if ($user->isDoctor()) {
                    // Fizjoterapeuta przypisuje siebie
                    $appointment->doctor_id = $user->id;
                } elseif ($user->isAdmin()) {
                    // Admin może wybrać lekarza lub przypisać pierwszego dostępnego
                    $assignDoctorId = $request->input('doctor_id');

                    if ($assignDoctorId) {
                        // Sprawdź czy wybrany lekarz jest na liście dostępnych
                        if (!empty($availableDoctorIds) && !in_array($assignDoctorId, $availableDoctorIds)) {
                            return response()->json([
                                'error' => 'Wybrany fizjoterapeuta nie jest dostępny dla tej wizyty'
                            ], 422);
                        }
                        $appointment->doctor_id = $assignDoctorId;
                    } elseif (!empty($availableDoctorIds)) {
                        // Przypisz pierwszego dostępnego fizjoterapeutę
                        $appointment->doctor_id = $availableDoctorIds[0];
                    } else {
                        return response()->json([
                            'error' => 'Brak dostępnych fizjoterapeutów do przypisania. Wybierz fizjoterapeutę ręcznie.'
                        ], 422);
                    }
                }
                $appointment->save();
            }

            // Ustaw cenę jeśli podana
            $price = $request->input('price');
            if ($price !== null && is_numeric($price) && $price >= 0) {
                $appointment->price = $price;
                $appointment->save();

                // Utwórz płatność jeśli cena > 0
                if ($price > 0 && $appointment->patient_id) {
                    \App\Models\Payment::updateOrCreate(
                        ['appointment_id' => $appointment->id],
                        [
                            'user_id' => $appointment->patient_id,
                            'amount' => $price,
                            'currency' => 'PLN',
                            'status' => 'pending',
                            'payment_method' => 'stripe',
                            'description' => "Płatność za wizytę: {$appointment->title}",
                        ]
                    );
                }
            }

            // Potwierdź wizytę
            $appointment->confirm();

            // Oznacz powiadomienia innych lekarzy o tej wizycie jako przeczytane
            if (!empty($availableDoctorIds)) {
                $otherDoctorIds = array_filter($availableDoctorIds, fn($id) => $id != $user->id);
                if (!empty($otherDoctorIds)) {
                    \App\Models\Notification::whereIn('user_id', $otherDoctorIds)
                        ->where('type', 'reservation_request')
                        ->where('data->appointment_id', $appointment->id)
                        ->update(['is_read' => true, 'read_at' => now()]);
                }
            }

            // Powiadomienie dla pacjenta z informacją o przypisanym fizjoterapeucie
            $doctorName = $appointment->doctor ? $appointment->doctor->full_name : 'fizjoterapeuta';
            $notificationMessage = "Twoja wizyta w dniu " . $appointment->start_time->format('d.m.Y H:i') .
                " została potwierdzona. Przyjmie Cię: {$doctorName}.";

            // Dodaj informację o płatności jeśli cena jest ustawiona
            if ($appointment->price && $appointment->price > 0) {
                $notificationMessage .= " Koszt wizyty: " . number_format($appointment->price, 2, ',', ' ') . " PLN. Możesz opłacić wizytę online w sekcji Płatności.";
            }

            \App\Models\Notification::createForUser(
                $appointment->patient_id,
                'reservation_confirmed',
                'Rezerwacja potwierdzona',
                $notificationMessage,
                ['appointment_id' => $appointment->id],
                $appointment
            );

            return response()->json([
                'success' => true,
                'message' => 'Rezerwacja została potwierdzona i przypisana do Ciebie',
                'appointment' => $appointment->fresh()->load(['doctor', 'patient'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas potwierdzania rezerwacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Odrzuć wniosek o rezerwację (lekarz/admin)
     * Tylko admin może całkowicie odrzucić wizytę bez przypisanego lekarza
     * Fizjoterapeuta może tylko "zrezygnować" z listy dostępnych
     */
    public function rejectReservation(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if (!$user->isDoctor() && !$user->isAdmin()) {
            return response()->json([
                'error' => 'Brak uprawnień'
            ], 403);
        }

        if (!$appointment->isPending()) {
            return response()->json([
                'error' => 'Ta wizyta nie oczekuje na potwierdzenie'
            ], 409);
        }

        $metadata = $appointment->metadata ?? [];
        $availableDoctorIds = $metadata['available_doctor_ids'] ?? [];

        // Sprawdź uprawnienia
        $canReject = false;
        if ($user->isAdmin()) {
            $canReject = true;
        } elseif ($appointment->doctor_id === null) {
            // Wizyta bez przypisanego lekarza - fizjoterapeuta może tylko się wycofać z listy
            $canReject = in_array($user->id, $availableDoctorIds);
        } elseif ($appointment->doctor_id === $user->id) {
            $canReject = true;
        }

        if (!$canReject) {
            return response()->json([
                'error' => 'Nie masz uprawnień do odrzucenia tej wizyty'
            ], 403);
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
            // Jeśli wizyta nie ma przypisanego lekarza i to nie admin
            if ($appointment->doctor_id === null && !$user->isAdmin()) {
                // Usuń fizjoterapeutę z listy dostępnych
                $availableDoctorIds = array_filter($availableDoctorIds, fn($id) => $id != $user->id);
                $metadata['available_doctor_ids'] = array_values($availableDoctorIds);
                $metadata['declined_by'][] = [
                    'doctor_id' => $user->id,
                    'reason' => $request->reason,
                    'declined_at' => now()->toDateTimeString()
                ];
                $appointment->metadata = $metadata;
                $appointment->save();

                // Oznacz powiadomienie tego lekarza jako przeczytane
                \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'reservation_request')
                    ->where('data->appointment_id', $appointment->id)
                    ->update(['is_read' => true, 'read_at' => now()]);

                // Jeśli nie ma już żadnych dostępnych lekarzy - odrzuć całkowicie
                if (empty($availableDoctorIds)) {
                    $appointment->reject('Wszyscy dostępni fizjoterapeuci odrzucili wizytę. Ostatni powód: ' . $request->reason);

                    \App\Models\Notification::createForUser(
                        $appointment->patient_id,
                        'reservation_rejected',
                        'Rezerwacja odrzucona',
                        "Twój wniosek o wizytę w dniu " . $appointment->start_time->format('d.m.Y H:i') .
                        " został odrzucony - brak dostępnych fizjoterapeutów.",
                        ['appointment_id' => $appointment->id],
                        $appointment
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Zrezygnowałeś z wizyty. Brak innych dostępnych fizjoterapeutów - wizyta została odrzucona.',
                        'appointment' => $appointment->fresh()
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Zrezygnowałeś z tej wizyty. Wizyta czeka na potwierdzenie przez innych fizjoterapeutów.',
                    'remaining_doctors' => count($availableDoctorIds)
                ]);
            }

            // Admin lub przypisany lekarz - pełne odrzucenie
            $appointment->reject($request->reason);

            // Zwolnij miejsce w slocie jeśli był przypisany lekarz
            if ($appointment->doctor_id) {
                $slot = \App\Models\SlotAvailability::forDoctor($appointment->doctor_id)
                    ->onDate($appointment->start_time)
                    ->where('start_time', $appointment->start_time->format('H:i:s'))
                    ->first();

                if ($slot) {
                    $slot->decrementBookings();
                }
            }

            // Oznacz wszystkie powiadomienia o tej wizycie jako przeczytane
            if (!empty($availableDoctorIds)) {
                \App\Models\Notification::whereIn('user_id', $availableDoctorIds)
                    ->where('type', 'reservation_request')
                    ->where('data->appointment_id', $appointment->id)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }

            // Powiadomienie dla pacjenta
            \App\Models\Notification::createForUser(
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
     * Pobierz oczekujące wnioski o rezerwację (lekarz)
     * Dla fizjoterapeutów pokazuje wizyty:
     * - przypisane do nich (stary system)
     * - bez przypisanego lekarza, gdzie są na liście available_doctor_ids (nowy system)
     */
    public function getPendingReservations(Request $request)
    {
        $user = Auth::user();

        if (!$user->isDoctor() && !$user->isAdmin()) {
            return response()->json([
                'error' => 'Brak uprawnień'
            ], 403);
        }

        $query = Appointment::pending()
            ->patientRequests()
            ->with(['patient', 'doctor']);

        if ($user->isDoctor()) {
            // Pokaż wizyty:
            // 1. Przypisane bezpośrednio do tego lekarza (stary system)
            // 2. Bez przypisanego lekarza, gdzie lekarz jest na liście dostępnych (nowy system)
            $query->where(function($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->whereNull('doctor_id')
                           ->whereJsonContains('metadata->available_doctor_ids', $user->id);
                  });
            });
        }

        $appointments = $query->orderBy('start_time')->get();

        // Oznacz wizyty, które można "przejąć" (bez przypisanego lekarza)
        $appointments->each(function($appointment) use ($user) {
            $appointment->can_claim = $appointment->doctor_id === null;
            $availableDoctors = $appointment->metadata['available_doctor_ids'] ?? [];
            $appointment->available_doctors_count = count($availableDoctors);
        });

        // Oblicz statystyki
        // Dla fizjoterapeuty - statystyki tylko dla przypisanych wizyt
        $statsQuery = Appointment::query();
        if ($user->isDoctor()) {
            $statsQuery->where('doctor_id', $user->id);
        }

        $confirmedToday = (clone $statsQuery)
            ->where('reservation_status', 'confirmed')
            ->whereDate('confirmed_at', today())
            ->count();

        $rejectedToday = (clone $statsQuery)
            ->where('reservation_status', 'rejected')
            ->whereDate('rejected_at', today())
            ->count();

        $upcoming = (clone $statsQuery)
            ->where('status', 'scheduled')
            ->where('start_time', '>', now())
            ->confirmed()
            ->count();

        return response()->json([
            'success' => true,
            'pending' => $appointments,
            'confirmed_today' => $confirmedToday,
            'rejected_today' => $rejectedToday,
            'upcoming' => $upcoming
        ]);
    }

    /**
     * Pobierz wizyty pacjenta
     */
    public function getMyAppointments(Request $request)
    {
        $user = Auth::user();

        if (!$user->isPatient()) {
            return response()->json([
                'error' => 'Tylko dla pacjentów'
            ], 403);
        }

        $status = $request->get('status', 'all');

        $query = Appointment::where('patient_id', $user->id)
            ->with(['doctor']);

        if ($status === 'upcoming') {
            $query->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->confirmed();
        } elseif ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'past') {
            $query->where('start_time', '<', now());
        }

        $appointments = $query->orderBy('start_time', 'desc')->get();

        // Jeśli to żądanie AJAX, zwróć JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'appointments' => $appointments
            ]);
        }

        // W przeciwnym razie zwróć widok
        return view('reservation.my-appointments', compact('appointments'));
    }

    /**
     * Pokaż widok z dostępnymi slotami dla pacjenta
     */
    public function showPatientAvailableSlotsView()
    {
        return view('reservation.patient-slots');
    }

    /**
     * Pokaż widok z oczekującymi wnioskami dla lekarza
     */
    public function showPendingReservationsView()
    {
        $user = Auth::user();

        if (!$user->isDoctor() && !$user->isAdmin()) {
            abort(403, 'Brak uprawnień');
        }

        return view('doctor.reservations.pending');
    }
}


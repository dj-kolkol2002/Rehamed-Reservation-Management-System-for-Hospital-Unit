<?php

namespace App\Http\Controllers;

use App\Models\SlotAvailability;
use App\Models\BlockedSlot;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DoctorSlotManagementController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->middleware('auth');
        $this->middleware('role:doctor,admin');
        $this->availabilityService = $availabilityService;
    }

    /**
     * Generuj sloty automatycznie z harmonogramu
     */
    public function generateSlots(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'visibility' => 'required|in:public,restricted,hidden',
            'max_patients' => 'nullable|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $visibility = $request->visibility ?? 'public';
        $maxPatients = $request->max_patients ?? 1;

        try {
            $generated = $this->availabilityService->generateSlotsFromSchedule(
                $doctor,
                $startDate,
                $endDate,
                $visibility,
                $maxPatients
            );

            return response()->json([
                'success' => true,
                'message' => "Wygenerowano {$generated} slotów",
                'generated_count' => $generated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas generowania slotów',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobierz sloty lekarza
     */
    public function getSlots(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->addMonth();

        $slots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Zmień widoczność slotu
     */
    public function updateSlotVisibility(Request $request, SlotAvailability $slot)
    {
        $user = Auth::user();

        if ($user->isDoctor() && $slot->doctor_id !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'visibility' => 'required|in:public,restricted,hidden',
            'allowed_patient_ids' => 'nullable|array',
            'allowed_patient_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'visibility' => $request->visibility
            ];

            if ($request->visibility === 'restricted' && $request->has('allowed_patient_ids')) {
                $updateData['allowed_patient_ids'] = $request->allowed_patient_ids;
            }

            $slot->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Widoczność slotu zaktualizowana',
                'slot' => $slot->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas aktualizacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Usuń slot
     */
    public function deleteSlot(SlotAvailability $slot)
    {
        $user = Auth::user();

        if ($user->isDoctor() && $slot->doctor_id !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        // Sprawdź czy są rezerwacje
        if ($slot->current_bookings > 0) {
            return response()->json([
                'error' => 'Nie można usunąć slotu z istniejącymi rezerwacjami'
            ], 409);
        }

        try {
            $slot->delete();

            return response()->json([
                'success' => true,
                'message' => 'Slot został usunięty'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas usuwania',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zablokuj przedział czasu
     */
    public function blockTimeSlot(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'reason' => 'required|in:personal,vacation,sick_leave,training,emergency,other',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $blockedSlot = BlockedSlot::create([
                'doctor_id' => $doctor->id,
                'start_time' => Carbon::parse($request->start_time),
                'end_time' => Carbon::parse($request->end_time),
                'reason' => $request->reason,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Przedział czasu został zablokowany',
                'blocked_slot' => $blockedSlot
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas blokowania',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobierz zablokowane przedziały
     */
    public function getBlockedSlots(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->addMonth();

        $blockedSlots = BlockedSlot::forDoctor($doctor->id)
            ->between($startDate, $endDate)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'blocked_slots' => $blockedSlots
        ]);
    }

    /**
     * Usuń blokadę
     */
    public function deleteBlockedSlot(BlockedSlot $blockedSlot)
    {
        $user = Auth::user();

        if ($user->isDoctor() && $blockedSlot->doctor_id !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        try {
            $blockedSlot->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blokada została usunięta'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas usuwania',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobierz statystyki dostępności
     */
    public function getAvailabilityStats(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $stats = $this->availabilityService->getAvailabilityStats($doctor, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Dodaj pacjenta do restricted slotu
     */
    public function addPatientToSlot(Request $request, SlotAvailability $slot)
    {
        $user = Auth::user();

        if ($user->isDoctor() && $slot->doctor_id !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $slot->addAllowedPatient($request->patient_id);

            return response()->json([
                'success' => true,
                'message' => 'Pacjent dodany do slotu',
                'slot' => $slot->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas dodawania pacjenta',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Usuń pacjenta z restricted slotu
     */
    public function removePatientFromSlot(Request $request, SlotAvailability $slot)
    {
        $user = Auth::user();

        if ($user->isDoctor() && $slot->doctor_id !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $slot->removeAllowedPatient($request->patient_id);

            return response()->json([
                'success' => true,
                'message' => 'Pacjent usunięty ze slotu',
                'slot' => $slot->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas usuwania pacjenta',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pokaż widok zarządzania slotami
     */
    public function showManageView()
    {
        return view('doctor.slots.manage');
    }

    /**
     * Pobierz moje sloty (alias dla getSlots z filtrami)
     */
    public function getMySlots(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->addMonth();

        $query = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'));

        // Filtry
        if ($request->has('visibility') && $request->visibility !== '') {
            $query->where('visibility', $request->visibility);
        }

        if ($request->has('is_available') && $request->is_available !== '') {
            $query->where('is_available', (bool)$request->is_available);
        }

        $slots = $query->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Pobierz statystyki slotów
     */
    public function getSlotStatistics(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->isDoctor() ? $user : User::find($request->doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doktor nie znaleziony'], 404);
        }

        $total = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->count();

        $publicSlots = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('visibility', 'public')
            ->count();

        $booked = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('current_bookings', '>', 0)
            ->sum('current_bookings');

        $available = SlotAvailability::forDoctor($doctor->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->available()
            ->count();

        return response()->json([
            'success' => true,
            'total' => $total,
            'public' => $publicSlots,
            'booked' => $booked,
            'available' => $available
        ]);
    }

    /**
     * Masowa aktualizacja widoczności slotów
     */
    public function bulkUpdateVisibility(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'slot_ids' => 'required|array',
            'slot_ids.*' => 'exists:slot_availability,id',
            'visibility' => 'required|in:public,restricted,hidden'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $slotIds = $request->slot_ids;
            $visibility = $request->visibility;

            // Sprawdź czy wszystkie sloty należą do lekarza
            if ($user->isDoctor()) {
                $invalidSlots = SlotAvailability::whereIn('id', $slotIds)
                    ->where('doctor_id', '!=', $user->id)
                    ->count();

                if ($invalidSlots > 0) {
                    return response()->json([
                        'error' => 'Niektóre sloty nie należą do Ciebie'
                    ], 403);
                }
            }

            // Aktualizuj widoczność
            $updated = SlotAvailability::whereIn('id', $slotIds)
                ->update(['visibility' => $visibility]);

            return response()->json([
                'success' => true,
                'message' => "Zaktualizowano widoczność {$updated} slotów",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas aktualizacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zaktualizuj listę dozwolonych pacjentów dla slotu
     */
    public function updateAllowedPatients(Request $request, SlotAvailability $slot)
    {
        $user = Auth::user();

        if ($user->isDoctor() && $slot->doctor_id !== $user->id) {
            return response()->json(['error' => 'Brak dostępu'], 403);
        }

        $validator = Validator::make($request->all(), [
            'patient_ids' => 'required|array',
            'patient_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Nieprawidłowe dane',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $slot->update([
                'visibility' => 'restricted',
                'allowed_patient_ids' => $request->patient_ids
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lista pacjentów zaktualizowana',
                'slot' => $slot->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Błąd podczas aktualizacji',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

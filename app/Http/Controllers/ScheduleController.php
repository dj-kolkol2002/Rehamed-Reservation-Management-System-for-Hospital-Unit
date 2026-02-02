<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display schedules for the week
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isDoctor()) {
            $schedules = $this->getFullWeekSchedule($user->id);
            return view('schedules.index', compact('schedules'));
        }

        if ($user->isAdmin()) {
            $doctors = User::where('role', 'doctor')->orderBy('firstname')->get();
            return view('schedules.admin-index', compact('doctors'));
        }

        abort(403, 'Nie masz dostępu do tej strony.');
    }

    /**
     * Show schedule for specific doctor (admin view)
     */
    public function show($doctorId)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $user->id != $doctorId) {
            abort(403, 'Nie masz dostępu do tego harmonogramu.');
        }

        $doctor = User::where('role', 'doctor')->findOrFail($doctorId);
        $schedules = $this->getFullWeekSchedule($doctorId);

        return view('schedules.show', compact('doctor', 'schedules'));
    }

    /**
     * Update schedule (all days at once)
     */
    public function update(Request $request, $doctorId = null)
    {
        $user = Auth::user();
        $targetDoctorId = $doctorId ?? $user->id;

        Log::info('Schedule update request', [
            'user_id' => $user->id,
            'target_doctor_id' => $targetDoctorId,
            'request_data' => $request->all(),
        ]);

        if (!$user->isAdmin() && $user->id != $targetDoctorId) {
            abort(403, 'Nie masz dostępu do tego harmonogramu.');
        }

        if (!$request->has('schedules')) {
            Log::error('No schedules data in request');
            return back()->withErrors(['schedules' => 'Brak danych harmonogramu w żądaniu.'])->withInput();
        }

        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.is_active' => 'nullable|in:0,1',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
        ]);

        Log::info('Validated schedule data', $validated);

        foreach ($validated['schedules'] as &$schedule) {
            $schedule['is_active'] = (bool) ($schedule['is_active'] ?? false);
        }

        try {
            foreach ($validated['schedules'] as $scheduleData) {
                $dayOfWeek = $scheduleData['day_of_week'];

                if ($scheduleData['is_active']) {
                    if (empty($scheduleData['start_time']) || empty($scheduleData['end_time'])) {
                        return back()->withErrors([
                            'schedules' => 'Dla dni czynnych musisz podać godziny rozpoczęcia i zakończenia pracy.'
                        ])->withInput();
                    }

                    if ($scheduleData['end_time'] <= $scheduleData['start_time']) {
                        return back()->withErrors([
                            'schedules' => 'Godzina zakończenia musi być późniejsza niż godzina rozpoczęcia.'
                        ])->withInput();
                    }
                }

                $updateData = [
                    'is_active' => $scheduleData['is_active'],
                    'start_time' => Carbon::parse($scheduleData['start_time'] ?? '08:00'),
                    'end_time' => Carbon::parse($scheduleData['end_time'] ?? '16:00'),
                ];

                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $targetDoctorId,
                        'day_of_week' => $dayOfWeek,
                    ],
                    $updateData
                );
            }

            Log::info("Zaktualizowano harmonogram dla fizjoterapeuty ID: {$targetDoctorId}");

            return back()->with('success', 'Harmonogram został zaktualizowany pomyślnie.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji harmonogramu: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Wystąpił błąd podczas zapisywania harmonogramu.'])->withInput();
        }
    }

    /**
     * Set default schedule (Mon-Fri 8:00-16:00)
     */
    public function setDefault($doctorId = null)
    {
        $user = Auth::user();
        $targetDoctorId = $doctorId ?? $user->id;

        if (!$user->isAdmin() && $user->id != $targetDoctorId) {
            abort(403, 'Nie masz dostępu do tego harmonogramu.');
        }

        try {
            for ($day = 0; $day <= 6; $day++) {
                $isActive = ($day >= 1 && $day <= 5);

                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $targetDoctorId,
                        'day_of_week' => $day,
                    ],
                    [
                        'is_active' => $isActive,
                        'start_time' => Carbon::parse('08:00'),
                        'end_time' => Carbon::parse('16:00'),
                    ]
                );
            }

            Log::info("Ustawiono domyślny harmonogram dla fizjoterapeuty ID: {$targetDoctorId}");

            return back()->with('success', 'Ustawiono domyślny harmonogram (Pon-Pt 8:00-16:00).');
        } catch (\Exception $e) {
            Log::error('Błąd podczas ustawiania domyślnego harmonogramu: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->withErrors(['error' => 'Wystąpił błąd podczas ustawiania domyślnego harmonogramu: ' . $e->getMessage()]);
        }
    }

    /**
     * Clear entire schedule
     */
    public function clear($doctorId = null)
    {
        $user = Auth::user();
        $targetDoctorId = $doctorId ?? $user->id;

        if (!$user->isAdmin() && $user->id != $targetDoctorId) {
            abort(403, 'Nie masz dostępu do tego harmonogramu.');
        }

        try {
            DoctorSchedule::where('doctor_id', $targetDoctorId)->delete();

            Log::info("Wyczyszczono harmonogram dla fizjoterapeuty ID: {$targetDoctorId}");

            return back()->with('success', 'Harmonogram został wyczyszczony.');
        } catch (\Exception $e) {
            Log::error('Błąd podczas czyszczenia harmonogramu: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Wystąpił błąd podczas czyszczenia harmonogramu.']);
        }
    }

    /**
     * Get full week schedule for a doctor
     */
    private function getFullWeekSchedule($doctorId)
    {
        $schedules = [];
        $dayNames = [
            0 => 'Niedziela',
            1 => 'Poniedziałek',
            2 => 'Wtorek',
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota',
        ];

        for ($day = 0; $day <= 6; $day++) {
            $schedule = DoctorSchedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $day)
                ->first();

            if (!$schedule) {
                $schedule = new DoctorSchedule([
                    'doctor_id' => $doctorId,
                    'day_of_week' => $day,
                    'is_active' => false,
                    'start_time' => null,
                    'end_time' => null,
                ]);
            }

            $schedules[$day] = $schedule;
        }

        return $schedules;
    }
}

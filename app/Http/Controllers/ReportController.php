<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MedicalDocument;
use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,doctor');
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Podstawowe statystyki dla dashboardu raportów
        $stats = [
            'total_patients' => User::patients()->count(),
            'active_patients' => User::patients()->active()->count(),
            'total_doctors' => User::doctors()->count(),
            'total_documents' => MedicalDocument::count(),
            'documents_this_month' => MedicalDocument::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'documents_today' => MedicalDocument::whereDate('created_at', today())->count(),
        ];

        // Statystyki płatności
        $paymentStats = [
            'total_payments' => Payment::count(),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'this_month_amount' => Payment::where('status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'this_month_count' => Payment::where('status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->count(),
        ];

        // Statystyki dokumentów według typu
        $documentsByType = MedicalDocument::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Najaktywniejsze dni w tym miesiącu
        $documentsPerDay = MedicalDocument::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Płatności metodą
        $paymentsByMethod = Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        return view('reports.index', compact('stats', 'paymentStats', 'paymentsByMethod', 'documentsByType', 'documentsPerDay'));
    }

    /**
     * Raport pacjentów
     */
    public function patients(Request $request)
    {
        $user = Auth::user();
        $query = User::patients()->with(['patientDocuments']);

        // Filtry
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $patients = $query->orderBy('lastname')->get();

        // Statystyki raportu
        $reportStats = [
            'total_patients' => $patients->count(),
            'active_patients' => $patients->where('is_active', true)->count(),
            'with_documents' => $patients->filter(function($patient) {
                return $patient->patientDocuments->count() > 0;
            })->count(),
            'average_age' => $patients->filter(function($patient) {
                return $patient->date_of_birth;
            })->avg(function($patient) {
                return $patient->date_of_birth->age ?? 0;
            })
        ];

        if ($request->has('download') && $request->download === 'pdf') {
            return $this->generatePatientsPdf($patients, $reportStats, $request->all());
        }

        return view('reports.patients', compact('patients', 'reportStats', 'request'));
    }

    /**
     * Raport dokumentacji medycznej
     */
    public function documents(Request $request)
    {
        $user = Auth::user();
        $query = MedicalDocument::with(['patient', 'doctor']);

        // Filtrowanie według roli
        if ($user->role === 'doctor') {
            $query->byDoctor($user->id);
        }

        // Filtry
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('doctor_id') && $user->role === 'admin') {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('document_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('document_date', '<=', $request->date_to);
        }

        $documents = $query->orderBy('document_date', 'desc')->get();

        // Dane dla filtrów
        $patients = User::patients()->active()->get();
        $doctors = $user->role === 'admin' ? User::doctors()->active()->get() : collect();

        // Statystyki raportu
        $reportStats = [
            'total_documents' => $documents->count(),
            'by_type' => $documents->groupBy('type')->map->count(),
            'by_status' => $documents->groupBy('status')->map->count(),
            'with_files' => $documents->filter(function($doc) {
                return $doc->hasFile();
            })->count(),
        ];

        if ($request->has('download') && $request->download === 'pdf') {
            return $this->generateDocumentsPdf($documents, $reportStats, $request->all());
        }

        return view('reports.documents', compact('documents', 'patients', 'doctors', 'reportStats', 'request'));
    }

    /**
     * Raport statystyk systemu
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();

        // Tylko admin może zobaczyć pełne statystyki
        if ($user->role !== 'admin') {
            abort(403, 'Brak uprawnień do przeglądania statystyk systemu.');
        }

        $dateFrom = $request->get('date_from', now()->subMonths(6)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Statystyki użytkowników
        $userStats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'admins' => User::admins()->count(),
            'doctors' => User::doctors()->count(),
            'patients' => User::patients()->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Statystyki dokumentów
        $documentStats = [
            'total_documents' => MedicalDocument::count(),
            'documents_period' => MedicalDocument::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'by_status' => MedicalDocument::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')->pluck('count', 'status'),
            'by_type' => MedicalDocument::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')->pluck('count', 'type'),
        ];

        // Statystyki płatności
        $paymentStats = [
            'total_payments' => Payment::count(),
            'total_amount' => Payment::sum('amount'),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'completed_amount' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'period_payments' => Payment::whereBetween('paid_at', [$dateFrom, $dateTo])->count(),
            'period_amount' => Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$dateFrom, $dateTo])->sum('amount'),
        ];

        // Płatności metodą
        $paymentsByMethod = Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get()
            ->toArray();

        // Aktywność miesięczna
        $monthlyActivity = MedicalDocument::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Miesięczna aktywność płatności
        $monthlyPayments = Payment::selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, count(*) as count, sum(amount) as total')
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Najpopularniejsze typy dokumentów w okresie
        $popularTypes = MedicalDocument::select('type', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();

        if ($request->has('download') && $request->download === 'pdf') {
            return $this->generateStatisticsPdf($userStats, $documentStats, $paymentStats, $monthlyActivity, $monthlyPayments, $popularTypes, $request->all());
        }

        return view('reports.statistics', compact(
            'userStats', 'documentStats', 'paymentStats', 'paymentsByMethod', 'monthlyActivity', 'monthlyPayments', 'popularTypes', 'request'
        ));
    }

    /**
     * Generuj PDF raportu pacjentów
     */
    private function generatePatientsPdf($patients, $stats, $filters)
    {
        $pdf = Pdf::loadView('reports.pdf.patients', compact('patients', 'stats', 'filters'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false
            ]);

        $filename = 'raport_pacjentow_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generuj PDF raportu dokumentów
     */
    private function generateDocumentsPdf($documents, $stats, $filters)
    {
        $pdf = Pdf::loadView('reports.pdf.documents', compact('documents', 'stats', 'filters'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false
            ]);

        $filename = 'raport_dokumentow_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generuj PDF raportu statystyk
     */
    private function generateStatisticsPdf($userStats, $documentStats, $paymentStats, $monthlyActivity, $monthlyPayments, $popularTypes, $filters)
    {
        $pdf = Pdf::loadView('reports.pdf.statistics', compact('userStats', 'documentStats', 'paymentStats', 'monthlyActivity', 'monthlyPayments', 'popularTypes', 'filters'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false
            ]);

        $filename = 'raport_statystyk_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}

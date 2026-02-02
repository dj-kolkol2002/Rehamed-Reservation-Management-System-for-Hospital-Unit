<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista faktur
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $invoices = Invoice::with(['user', 'payment.appointment'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            return view('admin.invoices.index', compact('invoices'));
        } elseif ($user->role === 'doctor') {
            $invoices = Invoice::with(['user', 'payment.appointment'])
                ->whereHas('payment.appointment', function ($query) use ($user) {
                    $query->where('doctor_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            return view('doctor.invoices.index', compact('invoices'));
        } else {
            $invoices = Invoice::with(['payment.appointment'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            return view('user.invoices.index', compact('invoices'));
        }
    }

    /**
     * Wyświetl fakturę
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if ($user->role !== 'admin' &&
            $user->role !== 'doctor' &&
            $invoice->user_id !== $user->id) {
            abort(403, 'Nie masz uprawnień do wyświetlenia tej faktury');
        }

        $invoice->load(['user', 'payment.appointment.doctor']);

        $view = match($user->role) {
            'admin' => 'admin.invoices.show',
            'doctor' => 'doctor.invoices.show',
            default => 'user.invoices.show',
        };

        return view($view, compact('invoice'));
    }

    /**
     * Pobierz PDF faktury
     */
    public function download(Invoice $invoice)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if ($user->role !== 'admin' &&
            $user->role !== 'doctor' &&
            $invoice->user_id !== $user->id) {
            abort(403, 'Nie masz uprawnień do pobrania tej faktury');
        }

        // Jeśli faktura ma zapisany plik, zwróć go
        if ($invoice->file_path && Storage::exists($invoice->file_path)) {
            return Storage::download($invoice->file_path, "Faktura_{$invoice->invoice_number}.pdf");
        }

        // Jeśli nie ma pliku, wygeneruj PDF na żywo
        return $this->generatePdf($invoice);
    }

    /**
     * Wygeneruj PDF faktury
     */
    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['user', 'payment.appointment.doctor', 'payment.appointment.patient']);

        $company = config('stripe.company');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'company'));

        // Opcjonalnie zapisz PDF
        $fileName = "invoices/invoice_{$invoice->invoice_number}.pdf";
        Storage::put($fileName, $pdf->output());

        $invoice->update(['file_path' => $fileName]);

        return $pdf->download("Faktura_{$invoice->invoice_number}.pdf");
    }

    /**
     * Wyświetl podgląd faktury w przeglądarce
     */
    public function preview(Invoice $invoice)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if ($user->role !== 'admin' &&
            $user->role !== 'doctor' &&
            $invoice->user_id !== $user->id) {
            abort(403, 'Nie masz uprawnień do wyświetlenia tej faktury');
        }

        $invoice->load(['user', 'payment.appointment.doctor', 'payment.appointment.patient']);
        $company = config('stripe.company');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'company'));

        return $pdf->stream("Faktura_{$invoice->invoice_number}.pdf");
    }

    /**
     * Wyślij fakturę emailem
     */
    public function sendEmail(Invoice $invoice)
    {
        $user = Auth::user();

        // Tylko admin i doctor mogą wysyłać faktury
        if (!in_array($user->role, ['admin', 'doctor'])) {
            abort(403, 'Nie masz uprawnień do wysyłania faktur');
        }

        // Fizjoterapeuta może wysyłać tylko faktury swoich wizyt
        if ($user->role === 'doctor' &&
            $invoice->payment->appointment->doctor_id !== $user->id) {
            abort(403, 'Nie możesz wysłać tej faktury');
        }

        try {
            // TODO: Implementacja wysyłki emaila z fakturą
            // Mail::to($invoice->user->email)->send(new InvoiceMail($invoice));

            return back()->with('success', 'Faktura została wysłana na adres email pacjenta');
        } catch (\Exception $e) {
            return back()->with('error', 'Błąd podczas wysyłania faktury: ' . $e->getMessage());
        }
    }
}

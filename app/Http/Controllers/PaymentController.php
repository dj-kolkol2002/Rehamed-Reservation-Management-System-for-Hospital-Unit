<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['webhook']);
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Lista płatności - różne widoki dla różnych ról
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin widzi wszystkie płatności
            $payments = Payment::with(['user', 'appointment.doctor', 'appointment.patient']);

            // Filtry dla admina
            if ($request->filled('status')) {
                $payments->where('status', $request->input('status'));
            }
            if ($request->filled('method')) {
                $payments->where('payment_method', $request->input('method'));
            }
            if ($request->filled('search')) {
                $search = $request->input('search');
                $payments->whereHas('user', function($q) use ($search) {
                    $q->where('firstname', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%");
                });
            }

            $payments = $payments->orderBy('created_at', 'desc')->paginate(20);
            return view('admin.payments.index', compact('payments'));

        } elseif ($user->role === 'doctor') {
            // Fizjoterapeuta widzi wszystkie swoje wizyty (nie tylko płatności)
            $appointments = Appointment::with(['patient', 'payment'])
                ->where('doctor_id', $user->id)
                ->whereNotNull('patient_id')
                ->whereNotNull('price')
                ->where('price', '>', 0);

            // Filtry dla fizjoterapeuty
            if ($request->filled('status')) {
                $status = $request->input('status');
                $appointments->whereHas('payment', function($q) use ($status) {
                    if ($status === 'unpaid') {
                        $q->where(function($subq) {
                            $subq->where('status', '!=', 'completed')
                                 ->orWhereNull('status');
                        });
                    } else {
                        $q->where('status', $status);
                    }
                }, '>=', 0)->orWhereDoesntHave('payment');
            }

            if ($request->filled('method')) {
                $method = $request->input('method');
                $appointments->whereHas('payment', function($q) use ($method) {
                    $q->where('payment_method', $method);
                });
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $appointments->whereHas('patient', function($q) use ($search) {
                    $q->where('firstname', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%");
                });
            }

            $appointments = $appointments->orderBy('start_time', 'desc')->paginate(20);
            return view('doctor.payments.index', compact('appointments'));

        } else {
            // Pacjent widzi swoje wizyty wymagające płatności
            $appointments = Appointment::with(['doctor', 'payment'])
                ->where('patient_id', $user->id)
                ->whereNotNull('price')
                ->where('price', '>', 0);

            // Filtry dla pacjenta
            if ($request->filled('status')) {
                $status = $request->input('status');
                $appointments->whereHas('payment', function($q) use ($status) {
                    if ($status === 'unpaid') {
                        $q->where('status', '!=', 'completed');
                    } else {
                        $q->where('status', $status);
                    }
                }, '>=', 0)->orWhereDoesntHave('payment');
            }

            if ($request->filled('method')) {
                $method = $request->input('method');
                $appointments->whereHas('payment', function($q) use ($method) {
                    $q->where('payment_method', $method);
                });
            }

            $appointments = $appointments->orderBy('start_time', 'desc')->paginate(20);
            return view('user.payments.index', compact('appointments'));
        }
    }

    /**
     * Szczegóły płatności
     */
    public function show(Payment $payment)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if ($user->role !== 'admin' &&
            $user->role !== 'doctor' &&
            $payment->user_id !== $user->id) {
            abort(403, 'Nie masz uprawnień do wyświetlenia tej płatności');
        }

        $payment->load(['user', 'appointment.doctor', 'appointment.patient', 'invoice']);

        $view = match($user->role) {
            'admin' => 'admin.payments.show',
            'doctor' => 'doctor.payments.show',
            default => 'user.payments.show',
        };

        return view($view, compact('payment'));
    }

    /**
     * Inicjuj płatność Stripe dla wizyty
     */
    public function createCheckoutSession(Appointment $appointment)
    {
        $user = Auth::user();

        // Tylko pacjent może płacić za swoją wizytę
        if ($appointment->patient_id !== $user->id) {
            return back()->with('error', 'Nie możesz opłacić tej wizyty');
        }

        // Sprawdź czy wizyta ma cenę
        if (!$appointment->price || $appointment->price <= 0) {
            return back()->with('error', 'Wizyta nie ma ustalonej ceny');
        }

        // Sprawdź czy wizyta nie jest już opłacona
        if ($appointment->isPaid()) {
            return back()->with('info', 'Ta wizyta jest już opłacona');
        }

        try {
            // Utwórz lub zaktualizuj płatność
            $payment = Payment::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'user_id' => $user->id,
                    'amount' => $appointment->price,
                    'currency' => config('stripe.currency', 'pln'),
                    'status' => 'pending',
                    'payment_method' => 'stripe',
                    'description' => "Płatność za wizytę: {$appointment->title}",
                ]
            );

            // Utwórz sesję Stripe Checkout
            $session = StripeSession::create([
                'payment_method_types' => ['card', 'blik', 'p24'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => config('stripe.currency', 'pln'),
                        'product_data' => [
                            'name' => $appointment->title,
                            'description' => "Wizyta: {$appointment->type_display} - " .
                                           $appointment->start_time->format('d.m.Y H:i'),
                        ],
                        'unit_amount' => (int)($appointment->price * 100), // Stripe używa groszy
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.cancel', ['appointment' => $appointment->id]),
                'client_reference_id' => $payment->id,
                'customer_email' => $user->email,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'appointment_id' => $appointment->id,
                    'user_id' => $user->id,
                ],
            ]);

            // Zapisz ID sesji w metadanych płatności
            $payment->update([
                'metadata' => [
                    'stripe_session_id' => $session->id,
                ]
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Błąd podczas inicjowania płatności: ' . $e->getMessage());
        }
    }

    /**
     * Sukces płatności
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('calendar.index')->with('error', 'Brak identyfikatora sesji płatności');
        }

        try {
            $session = StripeSession::retrieve($sessionId);

            $payment = Payment::find($session->client_reference_id);

            if (!$payment) {
                return redirect()->route('calendar.index')->with('error', 'Nie znaleziono płatności');
            }

            // Aktualizuj płatność tylko jeśli nie jest już oznaczona jako completed
            if ($payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'paid_at' => now(),
                ]);

                // Wygeneruj fakturę
                $this->generateInvoice($payment);

                // Wyślij powiadomienie
                $this->sendPaymentNotification($payment);
            }

            return view('payments.success', compact('payment'));

        } catch (\Exception $e) {
            return redirect()->route('calendar.index')->with('error', 'Błąd podczas przetwarzania płatności: ' . $e->getMessage());
        }
    }

    /**
     * Anulowanie płatności
     */
    public function cancel(Appointment $appointment)
    {
        return redirect()->route('calendar.index')
            ->with('warning', 'Płatność została anulowana. Możesz spróbować ponownie później.');
    }

    /**
     * Webhook Stripe
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook signature verification failed'], 400);
        }

        // Obsłuż różne typy eventów
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Obsługa ukończonej sesji checkout
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $payment = Payment::where('metadata->stripe_session_id', $session->id)->first();

        if ($payment && $payment->status !== 'completed') {
            $payment->update([
                'status' => 'completed',
                'stripe_payment_intent_id' => $session->payment_intent,
                'paid_at' => now(),
            ]);

            $this->generateInvoice($payment);
            $this->sendPaymentNotification($payment);
        }
    }

    /**
     * Obsługa udanej płatności
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment && $payment->status !== 'completed') {
            $payment->markAsPaid();
        }
    }

    /**
     * Obsługa nieudanej płatności
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->markAsFailed();
        }
    }

    /**
     * Generuj fakturę dla płatności
     */
    protected function generateInvoice(Payment $payment)
    {
        if ($payment->invoice) {
            return; // Faktura już istnieje
        }

        $invoice = Invoice::create([
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => 'issued',
            'issued_at' => now(),
            'paid_at' => $payment->paid_at,
        ]);

        $invoice->markAsIssued();
        $invoice->markAsPaid();
    }

    /**
     * Wyślij powiadomienie o płatności
     */
    protected function sendPaymentNotification(Payment $payment)
    {
        // Powiadomienie dla pacjenta
        Notification::create([
            'user_id' => $payment->user_id,
            'type' => 'payment_completed',
            'title' => 'Płatność zrealizowana',
            'message' => "Płatność za wizytę \"{$payment->appointment->title}\" została zrealizowana pomyślnie.",
            'icon' => 'credit-card',
            'action_url' => route('payments.show', $payment),
        ]);

        // Powiadomienie dla fizjoterapeuty
        if ($payment->appointment && $payment->appointment->doctor) {
            Notification::create([
                'user_id' => $payment->appointment->doctor_id,
                'type' => 'payment_received',
                'title' => 'Otrzymano płatność',
                'message' => "Pacjent {$payment->user->firstname} {$payment->user->lastname} opłacił wizytę.",
                'icon' => 'credit-card',
                'action_url' => route('payments.show', $payment),
            ]);
        }
    }

    /**
     * Oznacz płatność jako gotówkową (tylko dla doctor i admin)
     */
    public function markAsCash(Appointment $appointment)
    {
        $user = Auth::user();

        // Tylko fizjoterapeuta i admin mogą oznaczać płatności jako gotówkowe
        if (!in_array($user->role, ['admin', 'doctor'])) {
            abort(403, 'Nie masz uprawnień do wykonania tej akcji');
        }

        // Fizjoterapeuta może oznaczać tylko swoje wizyty
        if ($user->role === 'doctor' && $appointment->doctor_id !== $user->id) {
            abort(403, 'Nie możesz oznaczyć płatności dla tej wizyty');
        }

        if (!$appointment->price || $appointment->price <= 0) {
            return back()->with('error', 'Wizyta nie ma ustalonej ceny');
        }

        if ($appointment->isPaid()) {
            return back()->with('info', 'Ta wizyta jest już opłacona');
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->patient_id,
                'amount' => $appointment->price,
                'currency' => 'PLN',
                'status' => 'completed',
                'payment_method' => 'cash',
                'description' => "Płatność gotówką za wizytę: {$appointment->title}",
                'paid_at' => now(),
            ]);

            $this->generateInvoice($payment);
            $this->sendPaymentNotification($payment);

            DB::commit();

            return back()->with('success', 'Płatność oznaczona jako opłacona gotówką');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Błąd podczas zapisywania płatności: ' . $e->getMessage());
        }
    }
}

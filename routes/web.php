<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorPatientController;
use App\Http\Controllers\MedicalDocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DoctorSlotManagementController;
use App\Http\Controllers\AdminReservationController;


Auth::routes(['verify' => true]);


Route::get('/logout', function () {
    return redirect()->route('login')->with('info', 'Aby się wylogować, użyj przycisku wylogowania.');
});


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');


Route::middleware(['auth'])->group(function () {




    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware(['role:admin', 'verified'])->name('admin.dashboard');


    Route::get('/doctor/dashboard', function () {
        return view('doctor.dashboard');
    })->middleware(['role:doctor', 'verified'])->name('doctor.dashboard');


    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->middleware(['role:user', 'verified'])->name('user.dashboard');



    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/avatar/upload', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    });



    Route::prefix('calendar')->middleware('verified')->group(function () {

        Route::get('/', [CalendarController::class, 'index'])->name('calendar.index');


        Route::get('/events', [CalendarController::class, 'getEvents'])->name('calendar.events');


        Route::middleware(['role:admin,doctor'])->group(function () {
            Route::post('/store', [CalendarController::class, 'store'])->name('calendar.store');
            Route::put('/{appointment}', [CalendarController::class, 'update'])->name('calendar.update');
            Route::delete('/{appointment}', [CalendarController::class, 'destroy'])->name('calendar.destroy');


            Route::patch('/{appointment}/cancel', [CalendarController::class, 'cancel'])->name('calendar.cancel');
            Route::patch('/{appointment}/complete', [CalendarController::class, 'complete'])->name('calendar.complete');
            Route::post('/{appointment}/move', [CalendarController::class, 'move'])->name('calendar.move');
            Route::post('/{appointment}/resize', [CalendarController::class, 'resize'])->name('calendar.resize');
        });


        Route::get('/doctors', [CalendarController::class, 'getDoctors'])->name('calendar.doctors');
        Route::get('/patients', [CalendarController::class, 'getPatients'])->name('calendar.patients');
        Route::get('/stats', [CalendarController::class, 'getStats'])->name('calendar.stats');
        Route::get('/upcoming', [CalendarController::class, 'getUpcoming'])->name('calendar.upcoming');

        // Nowe endpointy dla dostępności
        Route::get('/available-slots', [CalendarController::class, 'getAvailableSlotsForCalendar'])->name('calendar.available-slots');
        Route::get('/doctor-schedule', [CalendarController::class, 'getDoctorSchedule'])->name('calendar.doctor-schedule');

        Route::get('/{appointment}/details', [CalendarController::class, 'details'])->name('calendar.details');
        Route::get('/{appointment}', [CalendarController::class, 'show'])->name('calendar.show');
    });



    // Rezerwacja wizyt
    Route::prefix('reservation')->middleware('verified')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservation.index');
        Route::get('/my-list', [ReservationController::class, 'myReservations'])->name('reservation.my-list');
        Route::post('/store', [ReservationController::class, 'store'])->name('reservation.store');
        Route::get('/available-slots', [ReservationController::class, 'getAvailableSlots'])->name('reservation.available-slots');
        Route::get('/doctor-schedule', [ReservationController::class, 'getDoctorSchedule'])->name('reservation.doctor-schedule');
        Route::get('/available-days', [ReservationController::class, 'getAvailableDays'])->name('reservation.available-days');

        // Nowe akcje
        Route::patch('/{appointment}/complete', [ReservationController::class, 'complete'])->name('reservation.complete');
        Route::post('/{appointment}/send-message', [ReservationController::class, 'sendMessage'])->name('reservation.send-message');
        Route::post('/{appointment}/doctor-notes', [ReservationController::class, 'saveDoctorNotes'])->name('reservation.doctor-notes');
        Route::post('/{appointment}/patient-message', [ReservationController::class, 'sendPatientMessage'])->name('reservation.patient-message');
        Route::post('/{appointment}/set-reminder', [ReservationController::class, 'setReminder'])->name('reservation.set-reminder');

        // Nowy system rezerwacji - widoki i endpointy dla pacjentów
        Route::get('/patient/slots', [ReservationController::class, 'showPatientAvailableSlotsView'])->name('reservation.patient.slots.view');
        Route::get('/patient/available-slots', [ReservationController::class, 'getPatientAvailableSlots'])->name('reservation.patient.available-slots');
        Route::get('/suggested-slot', [ReservationController::class, 'getSuggestedSlot'])->name('reservation.suggested-slot');
        Route::post('/request', [ReservationController::class, 'createReservationRequest'])->name('reservation.request.create');
        Route::get('/my-appointments', [ReservationController::class, 'getMyAppointments'])->name('reservation.my-appointments');

        // Nowy system rezerwacji - widoki i endpointy dla lekarzy i adminów
        Route::middleware(['role:doctor,admin'])->group(function () {
            Route::get('/pending-view', [ReservationController::class, 'showPendingReservationsView'])->name('reservation.pending.view');
            Route::get('/pending', [ReservationController::class, 'getPendingReservations'])->name('reservation.pending');
            Route::post('/{appointment}/confirm', [ReservationController::class, 'confirmReservation'])->name('reservation.confirm');
            Route::post('/{appointment}/reject', [ReservationController::class, 'rejectReservation'])->name('reservation.reject');
        });

        // Alias dla doctor.reservations.pending (kompatybilność wsteczna i czytelność)
        Route::get('/doctor/reservations/pending', [ReservationController::class, 'showPendingReservationsView'])
            ->middleware(['role:doctor,admin'])
            ->name('doctor.reservations.pending');

        // Parametryczne trasy na końcu
        Route::get('/{appointment}', [ReservationController::class, 'show'])->name('reservation.show');
        Route::put('/{appointment}', [ReservationController::class, 'update'])->name('reservation.update');
        Route::patch('/{appointment}/cancel', [ReservationController::class, 'cancel'])->name('reservation.cancel');
    });



    Route::prefix('doctor')->middleware(['role:doctor', 'verified'])->group(function () {
        Route::resource('patients', DoctorPatientController::class, [
            'as' => 'doctor',
            'parameters' => ['patients' => 'patient']
        ]);


        Route::get('patients-api', [DoctorPatientController::class, 'getPatients'])
            ->name('doctor.patients.api');


        Route::post('patients/{patient}/avatar/upload', [DoctorPatientController::class, 'uploadAvatar'])
            ->name('doctor.patients.avatar.upload');
        Route::delete('patients/{patient}/avatar', [DoctorPatientController::class, 'deleteAvatar'])
            ->name('doctor.patients.avatar.delete');

        // Zarządzanie slotami i dostępnością
        Route::prefix('slots')->group(function () {
            Route::get('/manage', [DoctorSlotManagementController::class, 'showManageView'])->name('doctor.slots.manage');
            Route::post('/generate', [DoctorSlotManagementController::class, 'generateSlots'])->name('doctor.slots.generate');
            Route::get('/list', [DoctorSlotManagementController::class, 'getMySlots'])->name('doctor.slots.list');
            Route::put('/{slot}/visibility', [DoctorSlotManagementController::class, 'updateSlotVisibility'])->name('doctor.slots.visibility');
            Route::delete('/{slot}', [DoctorSlotManagementController::class, 'deleteSlot'])->name('doctor.slots.delete');
            Route::post('/block', [DoctorSlotManagementController::class, 'blockTimeSlot'])->name('doctor.slots.block');
            Route::get('/blocked', [DoctorSlotManagementController::class, 'getBlockedSlots'])->name('doctor.slots.blocked');
            Route::delete('/blocked/{blockedSlot}', [DoctorSlotManagementController::class, 'deleteBlockedSlot'])->name('doctor.slots.blocked.delete');
            Route::put('/{slot}/patients', [DoctorSlotManagementController::class, 'updateAllowedPatients'])->name('doctor.slots.patients');
            Route::get('/statistics', [DoctorSlotManagementController::class, 'getSlotStatistics'])->name('doctor.slots.statistics');
            Route::post('/bulk-update-visibility', [DoctorSlotManagementController::class, 'bulkUpdateVisibility'])->name('doctor.slots.bulk-visibility');
        });
    });



    Route::prefix('medical-documents')->middleware('verified')->group(function () {

        Route::get('/', [MedicalDocumentController::class, 'index'])->name('medical-documents.index');

        Route::get('/create', [MedicalDocumentController::class, 'create'])
            ->name('medical-documents.create');

        Route::post('/', [MedicalDocumentController::class, 'store'])
            ->name('medical-documents.store');

        Route::get('/{medicalDocument}', [MedicalDocumentController::class, 'show'])
            ->name('medical-documents.show');

        Route::get('/{medicalDocument}/edit', [MedicalDocumentController::class, 'edit'])
            ->name('medical-documents.edit');

        Route::put('/{medicalDocument}', [MedicalDocumentController::class, 'update'])
            ->name('medical-documents.update');

        Route::delete('/{medicalDocument}', [MedicalDocumentController::class, 'destroy'])
            ->name('medical-documents.destroy');


        Route::get('/{medicalDocument}/download', [MedicalDocumentController::class, 'download'])
            ->name('medical-documents.download');

        Route::delete('/{medicalDocument}/file', [MedicalDocumentController::class, 'deleteFile'])
            ->name('medical-documents.file.delete');


        Route::get('/api/documents', [MedicalDocumentController::class, 'getDocuments'])
            ->name('medical-documents.api');
    });



    Route::prefix('reports')->middleware(['role:admin,doctor', 'verified'])->group(function () {

        Route::get('/', [ReportController::class, 'index'])->name('reports.index');


        Route::get('/patients', [ReportController::class, 'patients'])->name('reports.patients');


        Route::get('/documents', [ReportController::class, 'documents'])->name('reports.documents');


        Route::get('/statistics', [ReportController::class, 'statistics'])
            ->middleware('role:admin')
            ->name('reports.statistics');
    });



    Route::prefix('admin')->middleware(['role:admin', 'verified'])->group(function () {
        Route::resource('users', AdminUserController::class, [
            'as' => 'admin'
        ]);


        Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('admin.users.toggle-status');


        Route::patch('users/{user}/activate', [AdminUserController::class, 'activate'])
            ->name('admin.users.activate');

        Route::patch('users/{user}/deactivate', [AdminUserController::class, 'deactivate'])
            ->name('admin.users.deactivate');


        Route::patch('users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])
            ->name('admin.users.verify-email');

        Route::post('users/{user}/avatar/upload', [AdminUserController::class, 'uploadAvatar'])
            ->name('admin.users.avatar.upload');
        Route::delete('users/{user}/avatar', [AdminUserController::class, 'deleteAvatar'])
            ->name('admin.users.avatar.delete');

        // Panel zarządzania rezerwacjami dla admina
        Route::get('reservations', [AdminReservationController::class, 'index'])->name('admin.reservations.index');
        Route::get('reservations/pending', [AdminReservationController::class, 'getPendingReservations'])->name('admin.reservations.pending');
        Route::get('reservations/doctors', [AdminReservationController::class, 'getDoctors'])->name('admin.reservations.doctors');
        Route::post('reservations/{appointment}/confirm', [AdminReservationController::class, 'confirm'])->name('admin.reservations.confirm');
        Route::post('reservations/{appointment}/reject', [AdminReservationController::class, 'reject'])->name('admin.reservations.reject');
        Route::put('reservations/{appointment}/reassign', [AdminReservationController::class, 'reassign'])->name('admin.reservations.reassign');

        // Godziny pracy kliniki
        Route::prefix('clinic-hours')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ClinicHoursController::class, 'index'])->name('admin.clinic-hours.index');
            Route::post('/update', [App\Http\Controllers\Admin\ClinicHoursController::class, 'update'])->name('admin.clinic-hours.update');
            Route::post('/default', [App\Http\Controllers\Admin\ClinicHoursController::class, 'setDefault'])->name('admin.clinic-hours.default');
        });
    });



    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/api/get', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('get');
        Route::get('/api/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        // Specific routes MUST come before parameterized routes
        Route::delete('/clear-read', [App\Http\Controllers\NotificationController::class, 'clearRead'])->name('clear-read');
        Route::delete('/delete-all', [App\Http\Controllers\NotificationController::class, 'deleteAll'])->name('delete-all');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('show');
    });



    Route::prefix('settings')->group(function () {

        Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');


        Route::post('/theme', [App\Http\Controllers\SettingsController::class, 'updateTheme'])->name('settings.update.theme');


        Route::get('/export-data', [App\Http\Controllers\SettingsController::class, 'exportData'])->name('settings.export.data');


        Route::delete('/delete-account', [App\Http\Controllers\SettingsController::class, 'deleteAccount'])->name('settings.delete.account');
    });



    // Harmonogramy pracy dla fizjoterapeutów
    Route::prefix('schedules')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/doctor/{doctorId}', [ScheduleController::class, 'show'])->name('schedules.show');
        Route::post('/update/{doctorId?}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::post('/default/{doctorId?}', [ScheduleController::class, 'setDefault'])->name('schedules.default');
        Route::delete('/clear/{doctorId?}', [ScheduleController::class, 'clear'])->name('schedules.clear');
    });



    Route::prefix('chat')->middleware('verified')->group(function () {

        Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');


        Route::get('/conversation/{conversation}', [App\Http\Controllers\ChatController::class, 'show'])
            ->name('chat.conversation');


        Route::get('/start/{recipient}', [App\Http\Controllers\ChatController::class, 'startConversation'])
            ->name('chat.start');


        Route::post('/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])
            ->name('chat.send');


        Route::get('/conversation/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])
            ->name('chat.messages');


        Route::post('/conversation/{conversation}/mark-read', [App\Http\Controllers\ChatController::class, 'markAsRead'])
            ->name('chat.mark-read');


        Route::delete('/conversation/{conversation}', [App\Http\Controllers\ChatController::class, 'deleteConversation'])
            ->name('chat.delete');


        Route::prefix('file')->group(function () {

            Route::get('/{message}/download', [App\Http\Controllers\ChatController::class, 'downloadFile'])
                ->name('chat.file.download');


            Route::get('/{message}/thumbnail', [App\Http\Controllers\ChatController::class, 'getThumbnail'])
                ->name('chat.file.thumbnail');


            Route::get('/{message}/image', [App\Http\Controllers\ChatController::class, 'getImage'])
                ->name('chat.file.image');
        });


        Route::prefix('api')->group(function () {

            Route::get('/conversations', [App\Http\Controllers\ChatController::class, 'getConversations'])
                ->name('chat.api.conversations');


            Route::get('/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])
                ->name('chat.api.unread');


            Route::get('/users/search', [App\Http\Controllers\ChatController::class, 'searchUsers'])
                ->name('chat.api.users.search');
        });
    });




    Route::get('/payments/success', [App\Http\Controllers\PaymentController::class, 'success'])
        ->name('payments.success')
        ->middleware('auth');

    Route::get('/payments/cancel/{appointment}', [App\Http\Controllers\PaymentController::class, 'cancel'])
        ->name('payments.cancel')
        ->middleware('auth');

    Route::prefix('payments')->middleware('verified')->group(function () {

        Route::get('/', [App\Http\Controllers\PaymentController::class, 'index'])
            ->name('payments.index');


        Route::post('/appointment/{appointment}/checkout', [App\Http\Controllers\PaymentController::class, 'createCheckoutSession'])
            ->name('payments.checkout')
            ->middleware('role:user');


        Route::post('/appointment/{appointment}/mark-cash', [App\Http\Controllers\PaymentController::class, 'markAsCash'])
            ->name('payments.mark-cash')
            ->middleware('role:admin,doctor');


        Route::get('/{payment}', [App\Http\Controllers\PaymentController::class, 'show'])
            ->name('payments.show');
    });


    Route::post('/stripe/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])
        ->name('stripe.webhook')
        ->withoutMiddleware(['auth', 'verified']);



    Route::prefix('invoices')->middleware('verified')->group(function () {

        Route::get('/', [App\Http\Controllers\InvoiceController::class, 'index'])
            ->name('invoices.index');


        Route::get('/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])
            ->name('invoices.show');


        Route::get('/{invoice}/download', [App\Http\Controllers\InvoiceController::class, 'download'])
            ->name('invoices.download');


        Route::get('/{invoice}/preview', [App\Http\Controllers\InvoiceController::class, 'preview'])
            ->name('invoices.preview');


        Route::post('/{invoice}/send-email', [App\Http\Controllers\InvoiceController::class, 'sendEmail'])
            ->name('invoices.send-email')
            ->middleware('role:admin,doctor');
    });
});

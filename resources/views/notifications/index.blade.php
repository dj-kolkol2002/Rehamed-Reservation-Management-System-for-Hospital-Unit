{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Powiadomienia')

@section('styles')
<style>
    /* Notification page light mode styles (default) */
    .notification-card {
        background-color: #ffffff !important;
        border-color: #e5e7eb !important;
    }

    .notification-item {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e5e7eb;
    }

    .notification-item:hover {
        background-color: #f3f4f6 !important;
    }

    .notification-unread {
        background-color: #f0f9ff !important;
        border-left: 4px solid #3b82f6 !important;
    }

    .notification-unread:hover {
        background-color: #e0f2fe !important;
    }

    /* Remove bottom border on last notification item */
    .notification-item:last-child {
        border-bottom: none !important;
    }

    /* Ensure text colors are visible in light mode */
    .notification-item h4 {
        color: #111827 !important;  /* gray-900 */
    }

    .notification-item p {
        color: #4b5563 !important;  /* gray-600 */
    }

    .notification-item .text-gray-500 {
        color: #6b7280 !important;  /* gray-500 */
    }

    /* Page title and headers */
    h1 {
        color: #111827 !important;  /* gray-900 */
    }

    h1 + p {
        color: #6b7280 !important;  /* gray-500 */
    }

    /* Stat cards */
    .stat-card {
        background-color: #f9fafb !important;  /* gray-50 - lepszy kontrast */
        border-color: #e5e7eb !important;
    }

    .stat-card p {
        color: #111827 !important;  /* Ensure numbers are visible */
    }

    .stat-card .text-sm {
        color: #6b7280 !important;  /* Ensure labels are visible */
    }

    /* Buttons */
    .bg-gray-300 {
        background-color: #d1d5db !important;
    }

    .bg-gray-300:hover {
        background-color: #9ca3af !important;
    }

    .text-gray-700 {
        color: #374151 !important;
    }

    /* Header buttons - light mode */
    .bg-indigo-100 {
        background-color: #e0e7ff !important;
    }

    .bg-indigo-100:hover {
        background-color: #c7d2fe !important;
    }

    .text-indigo-700 {
        color: #4338ca !important;
    }

    .bg-gray-100 {
        background-color: #f3f4f6 !important;
    }

    .bg-gray-100:hover {
        background-color: #e5e7eb !important;
    }

    /* Form elements in light mode */
    select, input[type="text"], input[type="search"] {
        background-color: #ffffff !important;
        color: #111827 !important;
        border-color: #d1d5db !important;
    }

    select:focus, input[type="text"]:focus, input[type="search"]:focus {
        background-color: #ffffff !important;
        border-color: #6366f1 !important;
    }

    /* Badge colors for light mode */
    .badge-blue { background-color: #dbeafe !important; color: #1e40af !important; }
    .badge-yellow { background-color: #fef3c7 !important; color: #92400e !important; }
    .badge-red { background-color: #fee2e2 !important; color: #991b1b !important; }
    .badge-orange { background-color: #ffedd5 !important; color: #9a3412 !important; }
    .badge-green { background-color: #d1fae5 !important; color: #065f46 !important; }
    .badge-purple { background-color: #f3e8ff !important; color: #6b21a8 !important; }
    .badge-indigo { background-color: #e0e7ff !important; color: #3730a3 !important; }
    .badge-gray { background-color: #f3f4f6 !important; color: #374151 !important; }

    /* Icon backgrounds for light mode */
    .icon-blue { background-color: #dbeafe !important; }
    .icon-yellow { background-color: #fef3c7 !important; }
    .icon-red { background-color: #fee2e2 !important; }
    .icon-orange { background-color: #ffedd5 !important; }
    .icon-green { background-color: #d1fae5 !important; }
    .icon-purple { background-color: #f3e8ff !important; }
    .icon-indigo { background-color: #e0e7ff !important; }
    .icon-gray { background-color: #f3f4f6 !important; }

    /* Icon text colors for light mode */
    .icon-blue i { color: #1e40af !important; font-size: 1.125rem !important; }
    .icon-yellow i { color: #b45309 !important; font-size: 1.125rem !important; }
    .icon-red i { color: #991b1b !important; font-size: 1.125rem !important; }
    .icon-orange i { color: #c2410c !important; font-size: 1.125rem !important; }
    .icon-green i { color: #047857 !important; font-size: 1.125rem !important; }
    .icon-purple i { color: #7c3aed !important; font-size: 1.125rem !important; }
    .icon-indigo i { color: #4f46e5 !important; font-size: 1.125rem !important; }
    .icon-gray i { color: #4b5563 !important; font-size: 1.125rem !important; }

    /* Notification page dark mode styles - respects user's theme preference */
    body.dark-mode .notification-card {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
    }

    body.dark-mode .notification-item {
        background-color: #1f2937 !important;
        border-bottom-color: #374151 !important;
    }

    body.dark-mode .notification-item:hover {
        background-color: #374151 !important;
    }

    body.dark-mode .notification-unread {
        background-color: rgba(37, 99, 235, 0.1) !important;
        border-left: 4px solid #3b82f6 !important;
    }

    body.dark-mode .notification-unread:hover {
        background-color: rgba(37, 99, 235, 0.15) !important;
    }

    /* Remove bottom border on last notification item in dark mode */
    body.dark-mode .notification-item:last-child {
        border-bottom: none !important;
    }

    /* Ensure text colors are visible in dark mode */
    body.dark-mode .notification-item h4 {
        color: #ffffff !important;
    }

    body.dark-mode .notification-item p {
        color: #9ca3af !important;  /* gray-400 */
    }

    body.dark-mode .stat-card {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
    }

    /* Form elements in dark mode */
    body.dark-mode select,
    body.dark-mode input[type="text"],
    body.dark-mode input[type="search"] {
        background-color: #374151 !important;
        color: #f3f4f6 !important;
        border-color: #4b5563 !important;
    }

    body.dark-mode select:focus,
    body.dark-mode input[type="text"]:focus,
    body.dark-mode input[type="search"]:focus {
        background-color: #374151 !important;
        border-color: #6366f1 !important;
    }

    /* Buttons in dark mode */
    body.dark-mode .bg-gray-300 {
        background-color: #4b5563 !important;  /* Średnio szary w ciemnym motywie */
    }

    body.dark-mode .bg-gray-300:hover {
        background-color: #6b7280 !important;  /* Jaśniejszy szary na hover */
    }

    body.dark-mode .text-gray-700 {
        color: #d1d5db !important;  /* Jasny tekst w ciemnym motywie */
    }

    /* Badge colors for dark mode */
    body.dark-mode .badge-blue { background-color: #1e40af !important; color: #93c5fd !important; }
    body.dark-mode .badge-yellow { background-color: #a16207 !important; color: #fde047 !important; }
    body.dark-mode .badge-red { background-color: #991b1b !important; color: #fca5a5 !important; }
    body.dark-mode .badge-orange { background-color: #9a3412 !important; color: #fdba74 !important; }
    body.dark-mode .badge-green { background-color: #166534 !important; color: #86efac !important; }
    body.dark-mode .badge-purple { background-color: #6b21a8 !important; color: #d8b4fe !important; }
    body.dark-mode .badge-indigo { background-color: #3730a3 !important; color: #c7d2fe !important; }
    body.dark-mode .badge-gray { background-color: #374151 !important; color: #d1d5db !important; }

    /* Icon backgrounds for dark mode */
    body.dark-mode .icon-blue { background-color: #1e3a8a !important; }
    body.dark-mode .icon-yellow { background-color: #854d0e !important; }
    body.dark-mode .icon-red { background-color: #7f1d1d !important; }
    body.dark-mode .icon-orange { background-color: #7c2d12 !important; }
    body.dark-mode .icon-green { background-color: #14532d !important; }
    body.dark-mode .icon-purple { background-color: #581c87 !important; }
    body.dark-mode .icon-indigo { background-color: #312e81 !important; }
    body.dark-mode .icon-gray { background-color: #1f2937 !important; }

    /* Icon text colors for dark mode */
    body.dark-mode .icon-blue i { color: #93c5fd !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-yellow i { color: #fde047 !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-red i { color: #fca5a5 !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-orange i { color: #fdba74 !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-green i { color: #86efac !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-purple i { color: #d8b4fe !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-indigo i { color: #c7d2fe !important; font-size: 1.125rem !important; }
    body.dark-mode .icon-gray i { color: #d1d5db !important; font-size: 1.125rem !important; }

    /* Modal and Toast styles for light mode */
    #confirmationModal {
        backdrop-filter: blur(4px);
    }

    #confirmationModal .modal-content {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
    }

    #confirmationModal .modal-header {
        border-bottom-color: #e5e7eb;
    }

    #confirmationModal .modal-footer {
        border-top-color: #e5e7eb;
    }

    #confirmationModal .modal-title {
        color: #111827;
    }

    #confirmationModal .modal-message {
        color: #6b7280;
    }

    #toast {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    #toast .toast-message {
        color: #111827;
    }

    /* Dark mode overrides */
    body.dark-mode #confirmationModal .modal-content {
        background-color: #1f2937;
        border-color: #374151;
    }

    body.dark-mode #confirmationModal .modal-header {
        border-bottom-color: #374151;
    }

    body.dark-mode #confirmationModal .modal-footer {
        border-top-color: #374151;
    }

    body.dark-mode #confirmationModal .modal-title {
        color: #f9fafb;
    }

    body.dark-mode #confirmationModal .modal-message {
        color: #d1d5db;
    }

    body.dark-mode #toast {
        background-color: #1f2937;
        border-color: #374151;
    }

    body.dark-mode #toast .toast-message {
        color: #f9fafb;
    }

    /* Modal and Toast animations */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes modalSlideIn {
        from {
            transform: scale(0.95) translateY(-10px);
            opacity: 0;
        }
        to {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    @keyframes toastSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    #confirmationModal {
        animation: modalFadeIn 0.2s ease-out;
    }

    #confirmationModal > div {
        animation: modalSlideIn 0.3s ease-out;
    }

    #toast:not(.hidden) {
        animation: toastSlideIn 0.3s ease-out;
    }

    /* Mobile/Responsive styles for notifications */
    @media (max-width: 768px) {
        /* Header buttons on mobile */
        .flex.flex-wrap.gap-2 {
            width: 100%;
            justify-content: flex-start;
        }

        .flex.flex-wrap.gap-2 button,
        .flex.flex-wrap.gap-2 a {
            font-size: 0.875rem !important;
            padding: 0.5rem 0.75rem !important;
        }

        .notification-item {
            padding: 1rem !important;
        }

        .notification-item h4 {
            font-size: 0.875rem !important;
        }

        .notification-item p {
            font-size: 0.8125rem !important;
        }

        /* Make action buttons more mobile-friendly */
        .notification-item .flex.items-center.justify-between.mt-3 {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.75rem;
        }

        .notification-item .flex.items-center.space-x-2:last-child {
            width: 100%;
            justify-content: space-between;
            gap: 0.5rem;
        }

        /* Mobile button styles */
        .notification-item button,
        .notification-item a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.5rem;
            font-size: 0.75rem !important;
            white-space: nowrap;
        }

        /* "Oznacz jako przeczytane" button */
        .notification-item button[onclick*="markAsRead"] {
            background-color: #e0e7ff;
            color: #4338ca;
            font-weight: 500;
        }

        body.dark-mode .notification-item button[onclick*="markAsRead"] {
            background-color: #312e81;
            color: #a5b4fc;
        }

        /* "Zobacz szczegóły" button */
        .notification-item a[href] {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 500;
            flex: 1;
            text-align: center;
        }

        body.dark-mode .notification-item a[href] {
            background-color: #14532d;
            color: #86efac;
        }

        /* Delete button */
        .notification-item button[onclick*="deleteNotification"] {
            background-color: #fee2e2;
            color: #991b1b;
            width: 2.5rem;
            height: 2.5rem;
            padding: 0 !important;
        }

        body.dark-mode .notification-item button[onclick*="deleteNotification"] {
            background-color: #7f1d1d;
            color: #fca5a5;
        }

        /* Badge adjustments */
        .notification-item .badge-blue,
        .notification-item .badge-yellow,
        .notification-item .badge-red,
        .notification-item .badge-orange,
        .notification-item .badge-green,
        .notification-item .badge-purple,
        .notification-item .badge-indigo,
        .notification-item .badge-gray {
            font-size: 0.7rem;
            padding: 0.25rem 0.625rem;
        }
    }

    @media (max-width: 640px) {
        .notification-item {
            padding: 0.875rem !important;
        }

        .notification-item .w-10.h-10 {
            width: 2.25rem !important;
            height: 2.25rem !important;
        }

        .notification-item .icon-blue i,
        .notification-item .icon-yellow i,
        .notification-item .icon-red i,
        .notification-item .icon-orange i,
        .notification-item .icon-green i,
        .notification-item .icon-purple i,
        .notification-item .icon-indigo i,
        .notification-item .icon-gray i {
            font-size: 1rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex items-center justify-center px-2 sm:px-4 py-6 sm:py-8 bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-bell mr-2 text-indigo-600 dark:text-indigo-400"></i>
                    Powiadomienia
                </h1>
                <p class="text-gray-600 dark:text-gray-400">Zarządzaj swoimi powiadomieniami</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <button
                    id="mark-all-read-page"
                    class="bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:hover:bg-indigo-800 text-indigo-700 dark:text-indigo-300 px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    <i class="fas fa-check-double mr-2"></i>Oznacz wszystkie
                </button>
                <button
                    id="delete-all-notifications"
                    class="bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-700 dark:text-red-300 px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    <i class="fas fa-trash-alt mr-2"></i>Usuń wszystkie
                </button>
                <a href="{{ route('settings.index') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-cog mr-2"></i>Ustawienia
                </a>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-8">
            <div class="stat-card rounded-lg shadow border p-3 md:p-6">
                <div class="flex flex-col items-center sm:flex-row">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-4 text-center sm:text-left">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Wszystkie</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card rounded-lg shadow border p-3 md:p-6">
                <div class="flex flex-col items-center sm:flex-row">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-4 text-center sm:text-left">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Nieprzeczytane</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['unread'] }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card rounded-lg shadow border p-3 md:p-6">
                <div class="flex flex-col items-center sm:flex-row">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <i class="fas fa-calendar-day text-green-600 dark:text-green-400 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-4 text-center sm:text-left">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Dzisiaj</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today'] }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card rounded-lg shadow border p-3 md:p-6">
                <div class="flex flex-col items-center sm:flex-row">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:ml-4 text-center sm:text-left">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Ten tydzień</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $notifications->where('created_at', '>=', now()->startOfWeek())->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry -->
        <div class="notification-card rounded-lg shadow mb-6 p-6">
            <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-4 sm:space-y-0">
                <div class="flex-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Wszystkie</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Nieprzeczytane</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Przeczytane</option>
                    </select>
                </div>

                <div class="flex-1">
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Typ</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Wszystkie typy</option>
                        <option value="appointment_created" {{ request('type') === 'appointment_created' ? 'selected' : '' }}>Nowa wizyta</option>
                        <option value="appointment_updated" {{ request('type') === 'appointment_updated' ? 'selected' : '' }}>Zmiana wizyty</option>
                        <option value="appointment_cancelled" {{ request('type') === 'appointment_cancelled' ? 'selected' : '' }}>Anulowana wizyta</option>
                        <option value="appointment_reminder" {{ request('type') === 'appointment_reminder' ? 'selected' : '' }}>Przypomnienie</option>
                        <option value="document_created" {{ request('type') === 'document_created' ? 'selected' : '' }}>Nowy dokument</option>
                        <option value="document_updated" {{ request('type') === 'document_updated' ? 'selected' : '' }}>Zmiana dokumentu</option>
                        <option value="message_received" {{ request('type') === 'message_received' ? 'selected' : '' }}>Wiadomość</option>
                        <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>System</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filtruj
                    </button>
                    <a href="{{ route('notifications.index') }}" class="bg-gray-300 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Wyczyść
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista powiadomień -->
        <div class="notification-card rounded-lg shadow border">
            @if($notifications->count() > 0)
                <div>
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ !$notification->is_read ? 'notification-unread' : '' }} p-6 transition-colors"
                             data-notification-id="{{ $notification->id }}">
                            <div class="flex items-start space-x-4">
                                <div class="shrink-0">
                                    {{-- Debug: Type={{ $notification->type }}, Icon={{ $notification->default_icon }}, Color={{ $notification->color }} --}}
                                    <div class="w-10 h-10 rounded-full bg-{{ $notification->color }}-100 flex items-center justify-center icon-{{ $notification->color }}" style="display: flex; align-items: center; justify-content: center;">
                                        <i class="{{ $notification->default_icon }} text-{{ $notification->color }}-600 dark:text-{{ $notification->color }}-400" style="font-size: 1.125rem; display: inline-block;"></i>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                                            {{ $notification->title }}
                                            @if($notification->is_new)
                                                <span class="ml-2 inline-block w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                            @endif
                                            @if(isset($notification->data['priority']) && $notification->data['priority'] === 'high')
                                                <i class="ml-2 fas fa-exclamation text-red-500 text-xs"></i>
                                            @endif
                                        </h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->formatted_time }}</span>
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>

                                    <!-- Typ powiadomienia -->
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $notification->color }}-100 text-{{ $notification->color }}-800 badge-{{ $notification->color }}">
                                                @php
                                                    $typeNames = [
                                                        'appointment_created' => 'Nowa wizyta',
                                                        'appointment_updated' => 'Zmiana wizyty',
                                                        'appointment_cancelled' => 'Anulowanie',
                                                        'appointment_reminder' => 'Przypomnienie',
                                                        'document_created' => 'Nowy dokument',
                                                        'document_updated' => 'Aktualizacja dokumentu',
                                                        'message_received' => 'Wiadomość',
                                                        'user_registered' => 'Nowy użytkownik',
                                                        'system' => 'System'
                                                    ];
                                                @endphp
                                                {{ $typeNames[$notification->type] ?? ucfirst(str_replace('_', ' ', $notification->type)) }}
                                            </span>

                                            @if(!$notification->is_read)
                                                <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">• Nieprzeczytane</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            @if(!$notification->is_read)
                                                <button
                                                    onclick="markAsRead({{ $notification->id }})"
                                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
                                                >
                                                    <i class="fas fa-check mr-1"></i>Oznacz jako przeczytane
                                                </button>
                                            @endif

                                            @php
                                                // Generuj URL akcji na podstawie typu powiadomienia
                                                $actionUrl = '#';
                                                try {
                                                    if ($notification->action_url) {
                                                        $actionUrl = $notification->action_url;
                                                    } elseif (in_array($notification->type, ['appointment_created', 'appointment_updated', 'appointment_cancelled', 'appointment_reminder'])) {
                                                        // Przekieruj do strony szczegółów wizyty
                                                        $actionUrl = $notification->related_id ? route('calendar.details', $notification->related_id) : route('calendar.index');
                                                    } elseif (in_array($notification->type, ['document_created', 'document_updated'])) {
                                                        $actionUrl = $notification->related_id ? route('medical-documents.show', $notification->related_id) : route('medical-documents.index');
                                                    } elseif ($notification->type === 'message_received') {
                                                        $actionUrl = route('chat.index');
                                                    } elseif ($notification->type === 'user_registered' && Auth::user()->role === 'admin') {
                                                        $actionUrl = $notification->related_id ? route('admin.users.show', $notification->related_id) : route('dashboard');
                                                    } else {
                                                        $actionUrl = route('dashboard');
                                                    }
                                                } catch (\Exception $e) {
                                                    $actionUrl = route('dashboard');
                                                }
                                            @endphp

                                            @if($actionUrl && $actionUrl !== '#')
                                                <a href="{{ $actionUrl }}" class="text-xs text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>Zobacz szczegóły
                                                </a>
                                            @endif

                                            <button
                                                onclick="deleteNotification({{ $notification->id }})"
                                                class="text-xs text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                                title="Usuń powiadomienie"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Dodatkowe informacje w zależności od typu -->
                                    @if($notification->type === 'appointment_reminder' && $notification->related)
                                        <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <div class="flex items-center text-sm text-yellow-800">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span class="font-medium">
                                                    Wizyta: {{ $notification->related->start_time->format('d.m.Y H:i') }}
                                                </span>
                                                <span class="ml-2 text-xs">
                                                    ({{ $notification->related->start_time->diffForHumans() }})
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($notification->type === 'document_created' && $notification->related)
                                        <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center text-sm text-green-800">
                                                <i class="fas fa-file-medical mr-2"></i>
                                                <span class="font-medium">
                                                    Dokument: {{ $notification->related->type_display }}
                                                </span>
                                                <span class="ml-2 text-xs">
                                                    Status: {{ $notification->related->status_display }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginacja -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->withQueryString()->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Brak powiadomień</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if(request()->hasAny(['status', 'type']))
                            Nie znaleziono powiadomień pasujących do wybranych filtrów.
                        @else
                            Nie masz żadnych powiadomień do wyświetlenia.
                        @endif
                    </p>
                    @if(request()->hasAny(['status', 'type']))
                        <a href="{{ route('notifications.index') }}"
                           class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Wyczyść filtry
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="modal-content rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <!-- Modal Header -->
        <div class="modal-header px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="modal-title text-lg font-semibold">
                    Potwierdzenie
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4">
            <div class="flex items-start space-x-4">
                <div id="modalIcon" class="shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="flex-1">
                    <p id="modalMessage" class="modal-message text-sm">
                        Czy na pewno chcesz kontynuować?
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer px-6 py-4 border-t flex justify-end space-x-3">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Anuluj
            </button>
            <button id="modalConfirmBtn" onclick="confirmAction()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Usuń
            </button>
        </div>
    </div>
</div>

<!-- Success/Error Toast -->
<div id="toast" class="fixed top-4 right-4 rounded-lg shadow-lg px-6 py-4 hidden z-50 transform transition-all">
    <div class="flex items-center space-x-3">
        <div id="toastIcon" class="shrink-0">
            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
        </div>
        <p id="toastMessage" class="toast-message text-sm font-medium">
            Operacja zakończona pomyślnie
        </p>
    </div>
</div>

<script>
let modalCallback = null;

function showModal(title, message, confirmText, icon, confirmClass, callback) {
    const modal = document.getElementById('confirmationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    const modalIconContainer = document.getElementById('modalIcon');

    modalTitle.textContent = title;
    modalMessage.textContent = message;
    modalConfirmBtn.innerHTML = `<i class="fas ${icon} mr-2"></i>${confirmText}`;

    // Update button color
    modalConfirmBtn.className = `px-4 py-2 ${confirmClass} text-white rounded-lg font-medium transition-colors`;

    modalCallback = callback;
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    modalCallback = null;
}

function confirmAction() {
    if (modalCallback) {
        modalCallback();
    }
    closeModal();
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');

    toastMessage.textContent = message;

    if (type === 'success') {
        toastIcon.innerHTML = '<i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>';
    } else if (type === 'error') {
        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl"></i>';
    } else if (type === 'info') {
        toastIcon.innerHTML = '<i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl"></i>';
    }

    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Mark all as read button
    document.getElementById('mark-all-read-page')?.addEventListener('click', function() {
        showModal(
            'Oznacz wszystkie jako przeczytane',
            'Czy na pewno chcesz oznaczyć wszystkie powiadomienia jako przeczytane?',
            'Oznacz wszystkie',
            'fa-check-double',
            'bg-indigo-600 hover:bg-indigo-700',
            markAllAsRead
        );
    });

    // Delete all notifications button
    document.getElementById('delete-all-notifications')?.addEventListener('click', function() {
        showModal(
            'Usuń wszystkie powiadomienia',
            'Czy na pewno chcesz usunąć WSZYSTKIE powiadomienia? Ta operacja jest nieodwracalna!',
            'Usuń wszystkie',
            'fa-trash-alt',
            'bg-red-600 hover:bg-red-700',
            deleteAllNotifications
        );
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Close modal on background click
    document.getElementById('confirmationModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
});

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('notification-unread');
                const unreadBadge = item.querySelector('.text-indigo-600');
                if (unreadBadge && unreadBadge.textContent.includes('Nieprzeczytane')) {
                    unreadBadge.remove();
                }
                const markButton = item.querySelector('button[onclick*="markAsRead"]');
                if (markButton) {
                    markButton.remove();
                }
            }
            showToast('Powiadomienie zostało oznaczone jako przeczytane', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Wystąpił błąd podczas oznaczania powiadomienia', 'error');
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh page to show updated state
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Wystąpił błąd podczas oznaczania powiadomień', 'error');
    });
}

function deleteNotification(notificationId) {
    showModal(
        'Usuń powiadomienie',
        'Czy na pewno chcesz usunąć to powiadomienie?',
        'Usuń',
        'fa-trash',
        'bg-red-600 hover:bg-red-700',
        function() {
            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (item) {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(-100%)';
                        setTimeout(() => item.remove(), 300);
                    }
                    showToast('Powiadomienie zostało usunięte', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Wystąpił błąd podczas usuwania powiadomienia', 'error');
            });
        }
    );
}

function deleteAllNotifications() {
    fetch('/notifications/delete-all', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Refresh page to show empty state
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Błąd: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Wystąpił błąd podczas usuwania powiadomień', 'error');
    });
}

@php
function getTypeDisplayName($type) {
    $types = [
        'appointment_created' => 'Nowa wizyta',
        'appointment_updated' => 'Zmiana wizyty',
        'appointment_cancelled' => 'Anulowana wizyta',
        'appointment_reminder' => 'Przypomnienie',
        'document_created' => 'Nowy dokument',
        'document_updated' => 'Zmiana dokumentu',
        'message_received' => 'Wiadomość',
        'user_registered' => 'Nowy użytkownik',
        'system' => 'System'
    ];
    return $types[$type] ?? 'Powiadomienie';
}

function getActionUrl($notification) {
    if ($notification->action_url) {
        return $notification->action_url;
    }

    switch ($notification->type) {
        case 'appointment_created':
        case 'appointment_updated':
        case 'appointment_cancelled':
        case 'appointment_reminder':
            return $notification->related_id
                ? route('calendar.show', $notification->related_id)
                : route('calendar.index');

        case 'document_created':
        case 'document_updated':
            return $notification->related_id
                ? route('medical-documents.show', $notification->related_id)
                : route('medical-documents.index');

        case 'message_received':
            if ($notification->related && $notification->related->conversation) {
                return route('chat.conversation', $notification->related->conversation);
            }
            return route('chat.index');

        case 'user_registered':
            if (Auth::user()->isAdmin() && $notification->related_id) {
                return route('admin.users.show', $notification->related_id);
            }
            return '#';

        default:
            return '#';
    }
}
@endphp
</script>
@endsection

@push('styles')
<style>
.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animation for notification removal */
.notification-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Priority styles */
.notification-priority-high {
    border-left: 4px solid #ef4444 !important;
    background-color: #fef2f2 !important;
}

.notification-priority-medium {
    border-left: 4px solid #f59e0b !important;
    background-color: #fffbeb !important;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .notification-item {
        padding: 1rem;
    }

    .notification-item .flex {
        flex-direction: column;
        space-y: 0.5rem;
    }

    .notification-item .flex-shrink-0 {
        align-self: flex-start;
    }
}
</style>
@endpush

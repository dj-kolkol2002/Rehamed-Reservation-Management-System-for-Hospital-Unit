{{-- resources/views/doctor/dashboard.blade.php --}}
@extends('layouts.app')

@section('styles')
<style>
    /* Base styles - WHITE THEME BY DEFAULT */
    body {
        background-color: #f8fafc;
    }

    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* Main container white style */
    .dashboard-container {
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* Card backgrounds - WHITE */
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }

    /* Text colors for WHITE theme */
    .text-primary {
        color: #1e293b;
    }

    .text-secondary {
        color: #64748b;
    }

    /* Icon circles */
    .icon-circle {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-blue {
        background: rgba(59, 130, 246, 0.1);
    }

    .icon-green {
        background: rgba(16, 185, 129, 0.1);
    }

    .icon-purple {
        background: rgba(139, 92, 246, 0.1);
    }

    .icon-yellow {
        background: rgba(245, 158, 11, 0.1);
    }

    /* Quick actions */
    .quick-action-item {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05));
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .quick-action-item:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
    }

    .quick-action-item:hover .action-text {
        color: #ffffff !important;
    }

    .quick-action-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Section cards - WHITE */
    .section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
    }

    /* Chart container */
    .chart-container {
        position: relative;
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
    }

    .legend-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .legend-color {
        width: 1rem;
        height: 1rem;
        border-radius: 0.25rem;
        margin-right: 0.75rem;
    }

    .legend-label {
        display: flex;
        align-items: center;
        flex: 1;
        color: #64748b;
    }

    .legend-value {
        font-weight: 600;
        font-size: 1.125rem;
        color: #1e293b;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state-icon {
        width: 4rem;
        height: 4rem;
        background: rgba(99, 102, 241, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-scheduled {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .status-completed {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-cancelled {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    /* Appointment/User list item */
    .list-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .list-item:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    /* User role badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .role-admin {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .role-doctor {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .role-user {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
    }

    /* DARK MODE OVERRIDES */
    .dark-mode body {
        background-color: #0f172a;
    }

    .dark-mode .dashboard-container {
        background-color: #0f172a;
    }

    .dark-mode .stat-card,
    .dark-mode .section-card,
    .dark-mode .list-item {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-color: #334155;
    }

    .dark-mode .text-primary {
        color: #f1f5f9;
    }

    .dark-mode .text-secondary {
        color: #94a3b8;
    }

    .dark-mode .legend-label {
        color: #cbd5e1;
    }

    .dark-mode .legend-value {
        color: #f1f5f9;
    }

    .dark-mode .icon-blue {
        background: rgba(59, 130, 246, 0.2);
    }

    .dark-mode .icon-green {
        background: rgba(16, 185, 129, 0.2);
    }

    .dark-mode .icon-purple {
        background: rgba(139, 92, 246, 0.2);
    }

    .dark-mode .icon-yellow {
        background: rgba(245, 158, 11, 0.2);
    }

    .dark-mode .quick-action-item {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.15));
        border-color: rgba(99, 102, 241, 0.3);
    }

    .dark-mode .legend-item:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
</style>
@endsection

@section('content')
<div class="dashboard-container flex-1 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-primary mb-2">Panel Fizjoterapeuty</h1>
                <p class="text-secondary">Zarządzaj swoimi pacjentami i wizytami</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('calendar.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors duration-150">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Dodaj Wizytę
                </a>
                <a href="{{ route('doctor.patients.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors duration-150">
                    <i class="fas fa-user-plus mr-2"></i>
                    Nowy Pacjent
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $myPatients = \App\Models\User::where('role', 'user')->count();
            $myAppointmentsToday = \App\Models\Appointment::where('doctor_id', Auth::id())
                ->whereDate('start_time', today())
                ->count();
            $myUpcomingAppointments = \App\Models\Appointment::where('doctor_id', Auth::id())
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->count();
            $myDocuments = \App\Models\MedicalDocument::where('doctor_id', Auth::id())->count();
        @endphp

        <!-- Moi Pacjenci -->
        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Moi Pacjenci</p>
                    <p class="text-4xl font-bold text-primary">{{ $myPatients }}</p>
                </div>
                <div class="icon-circle icon-blue">
                    <i class="fas fa-users text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Dzisiaj -->
        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Dzisiaj</p>
                    <p class="text-4xl font-bold text-primary">{{ $myAppointmentsToday }}</p>
                </div>
                <div class="icon-circle icon-green">
                    <i class="fas fa-calendar-day text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Nadchodzące -->
        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Nadchodzące</p>
                    <p class="text-4xl font-bold text-primary">{{ $myUpcomingAppointments }}</p>
                </div>
                <div class="icon-circle icon-purple">
                    <i class="fas fa-clock text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Dokumenty -->
        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Dokumenty</p>
                    <p class="text-4xl font-bold text-primary">{{ $myDocuments }}</p>
                </div>
                <div class="icon-circle icon-yellow">
                    <i class="fas fa-file-medical text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Szybkie Akcje -->
        <div class="section-card">
            <h3 class="text-lg font-semibold text-primary mb-4">Szybkie Akcje</h3>
            <div class="space-y-3">
                <a href="{{ route('calendar.index') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-indigo-600 mr-3">
                        <i class="fas fa-calendar-plus text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Nowa Wizyta</p>
                        <p class="text-sm text-secondary action-text">Zaplanuj wizytę</p>
                    </div>
                </a>

                <a href="{{ route('doctor.patients.index') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-green-600 mr-3">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Pacjenci</p>
                        <p class="text-sm text-secondary action-text">Lista pacjentów</p>
                    </div>
                </a>

                <a href="{{ route('medical-documents.create') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-blue-600 mr-3">
                        <i class="fas fa-file-medical text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Dokumentacja</p>
                        <p class="text-sm text-secondary action-text">Nowy dokument</p>
                    </div>
                </a>

                <a href="{{ route('reports.index') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-purple-600 mr-3">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Raporty</p>
                        <p class="text-sm text-secondary action-text">Analiza danych</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Middle Column - Przegląd Systemu (Chart) -->
        <div class="section-card">
            <h3 class="text-lg font-semibold text-primary mb-4">Przegląd Systemu</h3>
            <div class="chart-container">
                <canvas id="doctorOverviewChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-label">
                        <div class="legend-color" style="background-color: #3b82f6;"></div>
                        <span>Nadchodzące Wizyty</span>
                    </div>
                    <span class="legend-value">{{ $myUpcomingAppointments }}</span>
                </div>
                <div class="legend-item">
                    <div class="legend-label">
                        <div class="legend-color" style="background-color: #10b981;"></div>
                        <span>Dokumenty Medyczne</span>
                    </div>
                    <span class="legend-value">{{ $myDocuments }}</span>
                </div>
            </div>
        </div>

        <!-- Right Column - Mój Dzisiejszy Harmonogram -->
        <div class="section-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-primary">Mój Dzisiejszy Harmonogram</h3>
                <a href="{{ route('calendar.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">
                    Pełny kalendarz
                </a>
            </div>

            @php
                $myTodayAppointments = \App\Models\Appointment::with(['patient'])
                    ->where('doctor_id', Auth::id())
                    ->whereDate('start_time', today())
                    ->orderBy('start_time')
                    ->limit(3)
                    ->get();
            @endphp

            @if($myTodayAppointments->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-calendar-check text-indigo-400 text-2xl"></i>
                    </div>
                    <p class="text-secondary mb-3">Brak wizyt na dzisiaj</p>
                    <a href="{{ route('calendar.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Dodaj wizytę
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($myTodayAppointments as $appointment)
                        <div class="flex items-center justify-between p-3 rounded-lg" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2);">
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="quick-action-icon bg-indigo-600 mr-3 shrink-0">
                                    <i class="fas fa-calendar text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-primary text-sm truncate">
                                        {{ $appointment->title }}
                                    </p>
                                    <p class="text-xs text-secondary truncate">
                                        {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}
                                        @if($appointment->patient)
                                            • {{ $appointment->patient->full_name }}
                                        @else
                                            • Blokada czasu
                                        @endif
                                    </p>
                                    @if($appointment->notes)
                                        <p class="text-xs text-secondary mt-1 truncate">{{ Str::limit($appointment->notes, 30) }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="status-badge status-{{ $appointment->status }} ml-2 shrink-0">
                                @if($appointment->status === 'scheduled')
                                    Zaplanowana
                                @elseif($appointment->status === 'completed')
                                    Ukończona
                                @elseif($appointment->status === 'cancelled')
                                    Anulowana
                                @else
                                    {{ ucfirst($appointment->status) }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Section - Ostatni Pacjenci -->
    <div class="section-card mt-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-primary">Ostatni Pacjenci</h3>
            <a href="{{ route('doctor.patients.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                Zobacz wszystkich
            </a>
        </div>

        @php
            $recentPatients = \App\Models\User::where('role', 'user')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        @endphp

        @if($recentPatients->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-injured text-blue-400 text-2xl"></i>
                </div>
                <p class="text-secondary">Brak pacjentów</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentPatients as $patient)
                    <a href="{{ route('doctor.patients.show', $patient) }}"
                       class="quick-action-item flex items-center">
                        <div class="quick-action-icon bg-blue-600 mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-primary text-sm truncate action-text">{{ $patient->full_name }}</p>
                            <p class="text-xs text-secondary action-text">{{ $patient->email }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const upcomingAppointments = {{ $myUpcomingAppointments }};
    const totalDocuments = {{ $myDocuments }};

    const ctx = document.getElementById('doctorOverviewChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Nadchodzące Wizyty', 'Dokumenty Medyczne'],
                datasets: [{
                    data: [upcomingAppointments, totalDocuments],
                    backgroundColor: ['#3b82f6', '#10b981'],
                    borderColor: '#1a202c',
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2d3748',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e0',
                        borderColor: '#374151',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
});
</script>
@endsection

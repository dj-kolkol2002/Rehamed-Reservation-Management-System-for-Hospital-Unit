
@extends('layouts.app')

@section('styles')
<style>

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


    .dashboard-container {
        background-color: #f8fafc;
        min-height: 100vh;
    }


    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }


    .text-primary {
        color: #1e293b;
    }

    .text-secondary {
        color: #64748b;
    }


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


    .section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
    }


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


    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
    }


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

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-primary mb-2">Witaj, {{ Auth::user()->firstname }}!</h1>
                <p class="text-secondary">Przeglądaj swoje wizyty i dokumentację medyczną</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('calendar.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors duration-150">
                    <i class="fas fa-calendar mr-2"></i>
                    Kalendarz Wizyt
                </a>
                <a href="{{ route('medical-documents.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-150">
                    <i class="fas fa-file-medical mr-2"></i>
                    Dokumentacja
                </a>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $myUpcomingAppointments = \App\Models\Appointment::where('patient_id', Auth::id())
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->count();

            $myCompletedAppointments = \App\Models\Appointment::where('patient_id', Auth::id())
                ->where('status', 'completed')
                ->count();

            $myDocuments = \App\Models\MedicalDocument::where('patient_id', Auth::id())
                ->where('is_private', false)
                ->count();

            $myNextAppointment = \App\Models\Appointment::where('patient_id', Auth::id())
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->orderBy('start_time')
                ->first();
        @endphp


        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Nadchodzące Wizyty</p>
                    <p class="text-4xl font-bold text-primary">{{ $myUpcomingAppointments }}</p>
                </div>
                <div class="icon-circle icon-blue">
                    <i class="fas fa-calendar-check text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>


        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Ukończone Wizyty</p>
                    <p class="text-4xl font-bold text-primary">{{ $myCompletedAppointments }}</p>
                </div>
                <div class="icon-circle icon-green">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>


        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Moje Dokumenty</p>
                    <p class="text-4xl font-bold text-primary">{{ $myDocuments }}</p>
                </div>
                <div class="icon-circle icon-purple">
                    <i class="fas fa-file-medical text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>


        <div class="stat-card p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary mb-1">Następna Wizyta</p>
                    <p class="text-4xl font-bold text-primary">
                        @if($myNextAppointment)
                            {{ $myNextAppointment->start_time->format('d.m') }}
                        @else
                            Brak
                        @endif
                    </p>
                    @if($myNextAppointment)
                        <p class="text-xs text-secondary mt-1">{{ $myNextAppointment->start_time->format('H:i') }}</p>
                    @endif
                </div>
                <div class="icon-circle icon-yellow">
                    <i class="fas fa-clock text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="section-card">
            <h3 class="text-lg font-semibold text-primary mb-4">Szybkie Akcje</h3>
            <div class="space-y-3">
                <a href="{{ route('calendar.index') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-indigo-600 mr-3">
                        <i class="fas fa-calendar text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Zobacz wizyty</p>
                        <p class="text-sm text-secondary action-text">Kalendarz</p>
                    </div>
                </a>

                <a href="{{ route('medical-documents.index') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-blue-600 mr-3">
                        <i class="fas fa-file-medical text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Przeglądaj dokumenty</p>
                        <p class="text-sm text-secondary action-text">Dokumentacja</p>
                    </div>
                </a>

                <a href="{{ route('profile.show') }}" class="quick-action-item flex items-center">
                    <div class="quick-action-icon bg-green-600 mr-3">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-primary action-text">Profil</p>
                        <p class="text-sm text-secondary action-text">Edytuj dane</p>
                    </div>
                </a>
            </div>
        </div>


        <div class="section-card">
            <h3 class="text-lg font-semibold text-primary mb-4">Przegląd Systemu</h3>
            <div class="chart-container">
                <canvas id="patientOverviewChart"></canvas>
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


        <div class="section-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-primary">Ostatnia Aktywność</h3>
                <a href="{{ route('calendar.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">
                    Zobacz kalendarz
                </a>
            </div>

            @php
                $recentAppointments = \App\Models\Appointment::with(['doctor'])
                    ->where('patient_id', Auth::id())
                    ->where('start_time', '>', now())
                    ->where('status', 'scheduled')
                    ->orderBy('start_time')
                    ->limit(3)
                    ->get();
            @endphp

            @if($recentAppointments->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-calendar-plus text-indigo-400 text-2xl"></i>
                    </div>
                    <p class="text-secondary mb-3">Nie masz zaplanowanych wizyt</p>
                    <a href="{{ route('calendar.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-calendar mr-2"></i>
                        Zobacz kalendarz
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentAppointments as $appointment)
                        <div class="flex items-center justify-between p-3 rounded-lg" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2);">
                            <div class="flex items-center flex-1 min-w-0">
                                <div class="quick-action-icon bg-indigo-600 mr-3 shrink-0">
                                    <i class="fas fa-calendar text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-primary text-sm truncate">
                                        {{ $appointment->doctor ? $appointment->doctor->full_name : 'Doktor' }}
                                    </p>
                                    <p class="text-xs text-secondary truncate">
                                        {{ $appointment->doctor ? $appointment->doctor->full_name : 'Doktor' }} • {{ $appointment->type }}
                                    </p>
                                    <p class="text-xs text-secondary mt-1">
                                        {{ $appointment->start_time->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <span class="status-badge status-zaplanowana ml-2 shrink-0">
                                Zaplanowana
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>


    <div class="section-card mt-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-primary">Najnowsze Dokumenty</h3>
            <a href="{{ route('medical-documents.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                Zobacz wszystkie
            </a>
        </div>

        @php
            $recentDocuments = \App\Models\MedicalDocument::where('patient_id', Auth::id())
                ->where('is_private', false)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        @endphp

        @if($recentDocuments->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-file-medical text-blue-400 text-2xl"></i>
                </div>
                <p class="text-secondary">Brak dokumentów</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($recentDocuments as $document)
                    <a href="{{ route('medical-documents.show', $document) }}"
                       class="quick-action-item flex items-center">
                        <div class="quick-action-icon bg-blue-600 mr-3">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-primary text-sm truncate action-text">{{ $document->title }}</p>
                            <p class="text-xs text-secondary action-text">{{ $document->created_at->format('d.m.Y') }}</p>
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

    const ctx = document.getElementById('patientOverviewChart');

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

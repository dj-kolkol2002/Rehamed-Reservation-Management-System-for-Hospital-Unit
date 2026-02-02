@extends('layouts.app')

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.8/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.8/main.min.css' rel='stylesheet' />

<style>

    /* Light mode - Main calendar container */
    #calendar {
        min-height: 700px;
        background: white;
        width: 100%;
    }

    /* Calendar wrapper */
    .bg-white.rounded-2xl.shadow-lg.p-3.md\\:p-6.card-hover {
        min-height: 750px;
        overflow: visible;
    }

    /* Calendar grid structure */
    .fc {
        font-family: inherit;
        height: 100% !important;
    }

    .fc .fc-daygrid {
        border-collapse: collapse;
    }

    .fc .fc-daygrid-body {
        position: relative;
    }

    .fc .fc-daygrid-day {
        background-color: #ffffff;
        position: relative;
        min-height: 100px;
    }

    /* Ensure scrollgrid displays properly */
    .fc .fc-scrollgrid {
        border-collapse: collapse;
        border: 1px solid #e5e7eb;
        background: white;
    }

    /* Month view (daygrid) styling */
    .fc .fc-daygrid-body table {
        width: 100%;
        border-collapse: collapse;
    }

    .fc .fc-daygrid-body tr {
        display: table-row;
    }

    .fc .fc-daygrid-day-frame {
        display: flex;
        flex-direction: column;
    }

    /* Ensure rows span full width */
    .fc .fc-daygrid tbody tr {
        display: table-row;
    }

    /* Day cells */
    .fc .fc-daygrid-day {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        padding: 4px;
    }

    .fc .fc-daygrid-day-bg {
        background: #fafafa;
    }

    .dark-mode #calendar {
    background-color: #1e293b;
    border-radius: 16px;
}

/* Dark mode - toolbar */
.dark-mode .fc .fc-toolbar {
    background: #1e293b;
    border: 1px solid #334155;
}

.dark-mode .fc .fc-toolbar-title {
    color: #f1f5f9;
}

/* Dark mode - calendar grid */
.dark-mode .fc .fc-scrollgrid {
    background: #1e293b;
    border-color: #334155;
}

.dark-mode .fc .fc-col-header-cell {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.15));
    color: #a5b4fc;
    border-color: #334155;
}

/* Dark mode - day cells */
.dark-mode .fc .fc-daygrid-day {
    background: #1e293b;
    border-color: #334155;
}

.dark-mode .fc .fc-daygrid-day:hover {
    background: rgba(99, 102, 241, 0.1);
}

.dark-mode .fc .fc-daygrid-day-number {
    color: #cbd5e1;
    font-weight: 700;
    font-size: 16px;
}

/* Dark mode - today */
.dark-mode .fc .fc-day-today {
    background: rgba(99, 102, 241, 0.15) !important;
}

.dark-mode .fc .fc-day-today .fc-daygrid-day-number {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

/* Dark mode - other month days */
.dark-mode .fc .fc-day-other .fc-daygrid-day-number {
    color: #64748b;
    font-weight: 600;
}

/* Dark mode - buttons */
.dark-mode .fc .fc-button-primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
}

.dark-mode .fc .fc-button-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

.dark-mode .fc .fc-button-primary:disabled {
    background: #475569;
    opacity: 0.5;
}

/* Dark mode - time grid */
.dark-mode .fc .fc-timegrid-slot {
    border-color: #334155;
}

.dark-mode .fc .fc-timegrid-slot-label {
    color: #94a3b8;
    border-color: #334155;
}

.dark-mode .fc .fc-timegrid-axis {
    background: #1e293b;
}

/* Dark mode - event colors remain vibrant */
.dark-mode .fc .fc-event {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Dark mode - popover */
.dark-mode .fc .fc-popover {
    background: #1e293b;
    border-color: #334155;
}

.dark-mode .fc .fc-popover-header {
    background: #334155;
    color: #f1f5f9;
}

.dark-mode .fc .fc-popover-body {
    color: #cbd5e1;
}

/* Dark mode - more link */
.dark-mode .fc .fc-daygrid-more-link {
    background: #475569;
    color: #e2e8f0;
}

.dark-mode .fc .fc-daygrid-more-link:hover {
    background: #64748b;
}

/* Remove white spaces */
.dark-mode .fc-theme-standard td,
.dark-mode .fc-theme-standard th {
    border-color: #334155;
}

.dark-mode .fc-theme-standard .fc-scrollgrid {
    border-color: #334155;
}

/* Calendar container background */
.dark-mode .calendar-container {
    background-color: #0f172a;
}

.dark-mode .bg-white {
    background-color: #1e293b !important;
}

.dark-mode .card-hover {
    border: 1px solid #334155;
}

/* Quick actions for mobile in dark mode */
.dark-mode .quick-action {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-color: rgba(99, 102, 241, 0.3);
}

.dark-mode .quick-action p {
    color: #cbd5e1;
}

.dark-mode .quick-action i {
    color: #a5b4fc;
}

.dark-mode .quick-action:hover {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

/* Modal in dark mode */
.dark-mode .modal-content {
    background-color: #1e293b;
    border: 1px solid #334155;
}

.dark-mode .modal-content h2 {
    color: #f1f5f9;
}

.dark-mode .form-input {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}

.dark-mode .form-input:focus {
    border-color: #6366f1;
    background: #1e293b;
}

.dark-mode label {
    color: #cbd5e1;
}

/* Better contrast for numbers */
.dark-mode .fc .fc-daygrid-day-number {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Week view in dark mode */
.dark-mode .fc .fc-timegrid-col {
    background: #1e293b;
}

.dark-mode .fc .fc-timegrid-col.fc-day-today {
    background: rgba(99, 102, 241, 0.1);
}

/* Event text in dark mode - ensure readability */
.dark-mode .fc .fc-event-title {
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
}

/* List view in dark mode */
.dark-mode .fc .fc-list {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
}

.dark-mode .fc .fc-list-table {
    background: #1e293b;
}

/* Day headers - dark simple and elegant */
.dark-mode .fc .fc-list-day-cushion {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.1));
    border-bottom-color: rgba(99, 102, 241, 0.25);
    color: #a5b4fc;
}

.dark-mode .fc .fc-list-day-text {
    color: #a5b4fc !important;
}

.dark-mode .fc .fc-list-day-side-text {
    color: #64748b !important;
}

/* Event rows - dark minimal */
.dark-mode .fc .fc-list-event {
    background: #1e293b;
    border-bottom: none;
}

.dark-mode .fc .fc-list-event:hover {
    background: #1e293b !important;
    border-left-color: #818cf8;
}

.dark-mode .fc .fc-list-event:hover td {
    background: transparent !important;
}

/* Time column - dark clean */
.dark-mode .fc .fc-list-event-time {
    color: #ffffff;
}

/* Title column - dark theme */
.dark-mode .fc .fc-list-event-title {
    color: #cbd5e1;
}

/* Event dot - dark simple */
.dark-mode .fc .fc-list-event-dot {
    /* Keep event color */
}

/* Empty state - dark theme */
.dark-mode .fc .fc-list-empty {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05));
    color: #94a3b8;
}

/* Notification in dark mode */
.dark-mode .notification {
    border: 1px solid #334155;
}

    /* Mobile-First Base Styles */
    .fc {
        font-family: inherit;
        font-size: 14px;
    }

    /* Podstawowe style dla wszystkich rozmiarów */
    .fc .fc-toolbar {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }

    .fc .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .fc .fc-button-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        min-height: 44px; /* iOS touch target minimum */
        min-width: 44px;
        touch-action: manipulation;
    }

    .fc .fc-button-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .fc .fc-button-primary:active {
        transform: translateY(0);
    }

    .fc .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        text-align: center;
        width: 100%;
        order: -1;
        margin: 0;
        padding: 0.5rem 0;
    }

    /* Calendar Grid Improvements */
    .fc .fc-scrollgrid {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .fc .fc-col-header-cell {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        font-weight: 700;
        color: #4338ca;
        padding: 0.75rem 0.25rem;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .fc .fc-daygrid-day {
        transition: all 0.3s ease;
        min-height: 60px;
    }

    .fc .fc-daygrid-day:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    .fc .fc-daygrid-day-number {
        font-size: 14px;
        padding: 0.5rem;
        font-weight: 600;
        color: #374151;
    }

    .fc .fc-day-today {
        background: rgba(102, 126, 234, 0.1) !important;
    }

    .fc .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0.25rem;
    }

    /* Event Styling */
    .fc .fc-event {
        border: none;
        border-radius: 8px;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 1px 2px;
        min-height: 24px;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .fc .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .fc .fc-event-title {
        font-size: 12px;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Event Types Colors */
    .fc .fc-event.fizjoterapia {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: white !important;
    }
    .fc .fc-event.konsultacja {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: white !important;
    }
    .fc .fc-event.masaz {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        color: white !important;
    }
    .fc .fc-event.neurorehabilitacja {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        color: white !important;
    }
    .fc .fc-event.kontrola {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: white !important;
    }

    /* Lepsze wyświetlanie wydarzeń w widoku miesięcznym */
    .fc .fc-daygrid-event {
        margin: 1px 2px;
        padding: 1px 4px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.3;
        border-radius: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-height: 16px;
        display: flex;
        align-items: center;
    }

    .fc .fc-daygrid-event .fc-event-title {
        font-size: 11px;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
        line-height: 1.2;
    }

    /* Lepsze wyświetlanie wydarzeń w widoku tygodniowym */
    .fc .fc-timegrid-event {
        border-radius: 4px;
        padding: 2px 4px;
        margin: 0 1px;
        font-size: 11px;
        line-height: 1.2;
        overflow: hidden;
        min-height: 18px;
    }

    .fc .fc-timegrid-event .fc-event-title {
        font-size: 11px;
        font-weight: 500;
        line-height: 1.2;
        word-wrap: break-word;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        hyphens: auto;
        word-break: break-word;
    }

    /* List view styles - light mode */
    .fc .fc-list {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .fc .fc-list-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    /* Day headers - simple and elegant */
    .fc .fc-list-day-cushion {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.12), rgba(118, 75, 162, 0.08));
        padding: 10px 20px;
        font-weight: 600;
        font-size: 13px;
        color: #667eea;
        border-bottom: 1px solid rgba(102, 126, 234, 0.2);
    }

    .fc .fc-list-day-text {
        color: #667eea !important;
        font-weight: 600;
    }

    .fc .fc-list-day-side-text {
        color: #9ca3af !important;
        font-size: 12px;
        font-weight: 500;
    }

    /* Event rows - clean and minimal */
    .fc .fc-list-event {
        transition: border-left-color 0.2s ease;
        border-left: 3px solid transparent;
        border-bottom: none;
        background: #ffffff;
    }

    .fc .fc-list-event:hover {
        background: #ffffff !important;
        border-left-color: #667eea;
    }

    .fc .fc-list-event:hover td {
        background: transparent !important;
    }

    /* Time column - clean */
    .fc .fc-list-event-time {
        font-weight: 600;
        color: #ffffff;
        font-size: 13px;
        padding: 12px 16px;
        min-width: 100px;
        white-space: nowrap;
    }

    /* Dot/graphic column - hidden */
    .fc .fc-list-event-graphic {
        display: none;
    }

    /* Title column - clean and readable */
    .fc .fc-list-event-title {
        font-weight: 500;
        font-size: 14px;
        padding: 12px 16px;
        color: #374151;
        line-height: 1.4;
    }

    /* Event dot - hidden */
    .fc .fc-list-event-dot {
        display: none;
    }

    /* Fix for overlapping text */
    .fc-list-event-time,
    .fc-list-event-title {
        display: table-cell;
        vertical-align: middle;
    }

    /* Empty state - friendly message */
    .fc .fc-list-empty {
        padding: 60px 24px;
        text-align: center;
        color: #9ca3af;
        font-size: 16px;
        font-weight: 500;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02), rgba(118, 75, 162, 0.02));
    }

    /* Krótkie wydarzenia w widoku tygodniowym */
    .fc .fc-timegrid-event.fc-event-short {
        padding: 1px 3px;
        font-size: 10px;
        min-height: 14px;
    }

    .fc .fc-timegrid-event.fc-event-short .fc-event-title {
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        -webkit-line-clamp: 1;
        line-height: 1;
    }

    /* Better event colors contrast */
    .fc .fc-event.fizjoterapia .fc-event-title {
        text-shadow: 0 0 2px rgba(0,0,0,0.3);
    }

    .fc .fc-event.konsultacja .fc-event-title {
        text-shadow: 0 0 2px rgba(0,0,0,0.3);
    }

    .fc .fc-event.masaz .fc-event-title {
        text-shadow: 0 0 2px rgba(0,0,0,0.3);
    }

    .fc .fc-event.neurorehabilitacja .fc-event-title {
        text-shadow: 0 0 2px rgba(0,0,0,0.3);
    }

    .fc .fc-event.kontrola .fc-event-title {
        text-shadow: 0 0 2px rgba(0,0,0,0.3);
    }

    /* Hover effects for better UX */
    .fc .fc-event:hover .fc-event-title {
        text-shadow: 0 0 3px rgba(255,255,255,0.5);
    }

    /* Dodatkowe style dla lepszej czytelności */
    .fc .fc-event .fc-event-title {
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Style dla bardzo krótkich wydarzeń */
    .fc .fc-event-short {
        font-weight: 700 !important;
    }

    .fc .fc-event-short .fc-event-title {
        font-weight: 700 !important;
        letter-spacing: -0.2px;
    }

    /* Lepsze dopasowanie tekstu */
    .fc .fc-event-title {
        text-align: left;
        vertical-align: middle;
        display: flex;
        align-items: center;
        min-height: inherit;
    }

    /* Status indicators */
    .fc .fc-event.status-cancelled {
        opacity: 0.6;
        filter: grayscale(30%);
    }

    .fc .fc-event.status-completed {
        border-left: 3px solid #22c55e;
    }

    .fc .fc-event.status-scheduled {
        border-left: 3px solid #3b82f6;
    }

    /* Today's events highlight */
    .fc .fc-event.today-event {
        box-shadow: 0 0 8px rgba(251, 191, 36, 0.4) !important;
        border: 2px solid #fbbf24 !important;
    }

    /* Multi-line text support for time grid events */
    .fc .fc-timegrid-event-harness .fc-event-title {
        height: auto;
        min-height: 12px;
    }

    /* Mobile Specific Styles */
    @media (max-width: 768px) {
        .calendar-container {
            padding: 0.75rem !important;
        }

        .fc {
            font-size: 13px;
        }

        .fc .fc-toolbar {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            gap: 0.75rem;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem;
        }

        .fc .fc-button-primary {
            padding: 0.625rem 0.875rem;
            font-size: 13px;
            min-height: 44px;
        }

        .fc .fc-button-group {
            display: flex;
            gap: 0.25rem;
        }

        .fc .fc-col-header-cell {
            padding: 0.5rem 0.125rem;
            font-size: 11px;
        }

        .fc .fc-daygrid-day {
            min-height: 50px;
        }

        .fc .fc-daygrid-day-number {
            font-size: 13px;
            padding: 0.375rem;
        }

        .fc .fc-event {
            font-size: 11px;
            padding: 2px 6px;
            margin: 0.5px 1px;
            min-height: 22px;
            border-radius: 6px;
        }

        .fc .fc-event-title {
            font-size: 11px;
        }

        .fc .fc-daygrid-more-link {
            font-size: 11px;
            padding: 2px 4px;
            border-radius: 4px;
            background: #6b7280;
            color: white;
            margin: 1px;
        }

        /* Hide week numbers on mobile */
        .fc .fc-daygrid-week-number {
            display: none;
        }

        /* Time grid mobile adjustments */
        .fc .fc-timegrid-slot {
            height: 35px;
        }

        .fc .fc-timegrid-slot-label {
            font-size: 11px;
            padding: 0 0.25rem;
        }

        .fc .fc-timegrid-event {
            font-size: 10px;
            padding: 1px 3px;
        }

        .fc .fc-timegrid-event .fc-event-title {
            font-size: 10px;
            -webkit-line-clamp: 2;
        }

        /* Mobile calendar height */
        #calendar {
            min-height: 400px !important;
            height: auto !important;
        }

        .fc .fc-view-harness {
            height: auto !important;
            min-height: 350px;
        }

        /* Responsywne dostosowania dla mobile - wydarzenia */
        .fc .fc-daygrid-event {
            font-size: 10px;
            padding: 0px 2px;
            margin: 0.5px 1px;
            min-height: 14px;
            line-height: 1.2;
        }

        .fc .fc-daygrid-event .fc-event-title {
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
        }

        .fc .fc-timegrid-event {
            font-size: 10px;
            padding: 1px 3px;
            min-height: 16px;
        }

        .fc .fc-timegrid-event .fc-event-title {
            font-size: 10px;
            -webkit-line-clamp: 2;
            line-height: 1.1;
            font-weight: 500;
        }

        .fc .fc-timegrid-event.fc-event-short .fc-event-title {
            font-size: 9px;
            -webkit-line-clamp: 1;
            font-weight: 600;
        }
    }

    /* Small mobile (iPhone SE, etc.) */
    @media (max-width: 480px) {
        .fc .fc-toolbar-title {
            font-size: 1rem;
        }

        .fc .fc-button-primary {
            padding: 0.5rem 0.75rem;
            font-size: 12px;
            min-height: 40px;
        }

        .fc .fc-col-header-cell {
            font-size: 10px;
            padding: 0.375rem 0.1rem;
        }

        .fc .fc-daygrid-day {
            min-height: 45px;
        }

        .fc .fc-daygrid-day-number {
            font-size: 12px;
            padding: 0.25rem;
        }

        .fc .fc-event {
            font-size: 10px;
            padding: 1px 4px;
            min-height: 20px;
        }

        .fc .fc-event-title {
            font-size: 10px;
        }

        .fc .fc-day-today .fc-daygrid-day-number {
            width: 28px;
            height: 28px;
        }

        /* Bardzo małe ekrany - ultra kompaktowe wydarzenia */
        .fc .fc-daygrid-event {
            font-size: 9px;
            padding: 0px 1px;
            margin: 0.5px;
            min-height: 12px;
            line-height: 1.1;
        }

        .fc .fc-daygrid-event .fc-event-title {
            font-size: 9px;
            font-weight: 700;
            line-height: 1.1;
        }

        .fc .fc-timegrid-event {
            font-size: 9px;
            padding: 1px 2px;
            min-height: 14px;
        }

        .fc .fc-timegrid-event .fc-event-title {
            font-size: 9px;
            -webkit-line-clamp: 1;
            line-height: 1;
            font-weight: 600;
        }

        .fc .fc-timegrid-event.fc-event-short .fc-event-title {
            font-size: 8px;
            font-weight: 700;
        }
    }

    /* Tablet adjustments */
    @media (min-width: 769px) and (max-width: 1024px) {
        .fc .fc-toolbar {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .fc .fc-toolbar-title {
            order: 0;
            width: auto;
            font-size: 1.5rem;
        }

        .fc .fc-button-primary {
            padding: 0.75rem 1.25rem;
            font-size: 14px;
        }
    }

    /* Desktop */
    @media (min-width: 1025px) {
        .fc .fc-toolbar {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
        }

        .fc .fc-toolbar-title {
            order: 0;
            width: auto;
            font-size: 1.75rem;
        }

        .fc .fc-button-primary {
            padding: 0.875rem 1.5rem;
            font-size: 15px;
        }

        .fc .fc-daygrid-day {
            min-height: 80px;
        }

        .fc .fc-event {
            font-size: 13px;
            padding: 6px 10px;
            min-height: 28px;
        }
    }

    /* Modal Styles - Mobile First */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .modal-content {
        background-color: white;
        margin: 1rem;
        padding: 1.5rem;
        border-radius: 20px;
        width: calc(100% - 2rem);
        max-width: 500px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
        position: relative;
        margin-left: auto;
        margin-right: auto;
        margin-top: 5rem;
        margin-bottom: 2rem;
    }

    @media (min-width: 769px) {
        .modal-content {
            margin: 5% auto;
            padding: 2rem;
            width: 90%;
        }
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .close {
        position: absolute;
        right: 1rem;
        top: 1rem;
        color: #6b7280;
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s ease;
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.05);
        line-height: 1;
        z-index: 10;
    }

    .close:hover {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    /* Quick Actions for Mobile */
    .quick-actions-mobile {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    @media (min-width: 769px) {
        .quick-actions-mobile {
            display: none;
        }
    }

    .quick-action {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        border: 1px solid rgba(102, 126, 234, 0.2);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        touch-action: manipulation;
    }

    .quick-action:active {
        transform: scale(0.98);
    }

    .quick-action:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }

    .quick-action i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #667eea;
        transition: color 0.3s ease;
    }

    .quick-action:hover i {
        color: white;
    }

    .quick-action p {
        margin: 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        transition: color 0.3s ease;
    }

    .quick-action:hover p {
        color: white;
    }

    /* Form Styling - Mobile First */
    .form-input {
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        padding: 0.875rem;
        font-size: 16px; /* Prevents zoom on iOS */
        transition: all 0.3s ease;
        width: 100%;
        touch-action: manipulation;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    /* Button Styling - Mobile First */
    .btn-primary, .btn-secondary, .btn-danger {
        border-radius: 12px;
        padding: 0.875rem 1.5rem;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 48px;
        touch-action: manipulation;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }

    @media (min-width: 640px) {
        .btn-primary, .btn-secondary, .btn-danger {
            width: auto;
        }
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: #f9fafb;
        color: #6b7280;
        border: 2px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #f3f4f6;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: none;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
    }

    /* Modal Button Layout */
    .modal-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
        align-items: center;
    }

    .modal-buttons .btn-primary,
    .modal-buttons .btn-danger {
        width: 90%;
        max-width: 300px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    @media (min-width: 640px) {
        .modal-buttons {
            flex-direction: row;
            justify-content: flex-end;
            align-items: auto;
        }

        .modal-buttons .btn-primary,
        .modal-buttons .btn-danger {
            width: auto;
            max-width: none;
            padding: 0.5rem 1rem;
        }
    }

    /* Loading and Animation States */
    .loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Touch improvements */
    @media (hover: none) and (pointer: coarse) {
        .fc .fc-event:hover {
            transform: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .quick-action:hover {
            transform: none;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            color: inherit;
        }
    }

    /* Accessibility */
    .fc .fc-event:focus,
    .quick-action:focus,
    .btn-primary:focus,
    .btn-secondary:focus,
    .btn-danger:focus,
    .form-input:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }

    /* Notification improvements for mobile */
    .notification {
        position: fixed;
        top: 1rem;
        right: 1rem;
        left: 1rem;
        z-index: 1100;
        padding: 1rem;
        padding-right: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        transform: translateY(-100px);
        opacity: 0;
    }

    @media (min-width: 640px) {
        .notification {
            left: auto;
            max-width: 500px;
        }
    }

    .notification.show {
        transform: translateY(0);
        opacity: 1;
    }

    .notification.success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .notification.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .notification.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .notification .close-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 24px;
        height: 24px;
        border: none;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: background 0.2s;
    }

    .notification .close-btn:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    /* Prevent zoom on double tap */
    * {
        touch-action: manipulation;
    }

    /* Improve scrolling on mobile */
    .modal {
        -webkit-overflow-scrolling: touch;
        overflow-scrolling: touch;
    }

    /* Better contrast for accessibility */
    .fc .fc-button-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Tooltip improvements */
    .fc .fc-event[title] {
        position: relative;
    }

    /* Print styles */
    @media print {
        .quick-actions-mobile,
        .modal,
        .notification {
            display: none !important;
        }

        .fc .fc-toolbar {
            background: white !important;
            box-shadow: none !important;
        }
    }

    /* ====== HIDE ALL-DAY ROW ====== */
    /* This is handled via allDaySlot: false in JavaScript config */
    /* All-day slot will not be displayed in any view */

</style>
@endsection

@section('content')
<div class="flex-1 calendar-container p-4 md:p-6">
    <!-- Quick Actions for Mobile -->
    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
    <div class="quick-actions-mobile">
        <button onclick="openModal('addEvent')" class="quick-action" style="grid-column: span 2;">
            <i class="fas fa-plus"></i>
            <p>Dodaj wizytę</p>
        </button>
    </div>
    @endif

    <!-- Calendar Container -->
    <div class="bg-white rounded-2xl shadow-lg p-3 md:p-6 card-hover">
        <!-- Desktop Header -->
        <div class="hidden md:flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-calendar-alt mr-3 text-indigo-600"></i>
                Terminarz
            </h1>
            <div class="flex space-x-3">
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
                <button onclick="openModal('addEvent')" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Dodaj wizytę
                </button>
                @endif
            </div>
        </div>

        <div id="calendar"></div>
    </div>

    <!-- Modal do dodawania/edycji wydarzeń -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="text-xl font-bold text-gray-900 mb-6 pr-12">Dodaj nową wizytę</h2>
            <form id="eventForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tytuł wizyty</label>
                    <input type="text" id="eventTitle" class="form-input" placeholder="Nazwa wizyty" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typ terapii</label>
                    <select id="eventType" class="form-input" required>
                        <option value="fizjoterapia">Fizjoterapia</option>
                        <option value="konsultacja">Konsultacja</option>
                        <option value="masaz">Masaż leczniczy</option>
                        <option value="neurorehabilitacja">Neurorehabilitacja</option>
                        <option value="kontrola">Wizyta kontrolna</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data rozpoczęcia</label>
                        <input
                            type="datetime-local"
                            id="eventStart"
                            class="form-input"
                            required
                            step="900"
                            min="{{ date('Y-m-d') }}T08:00"
                            onchange="validateTimeInputs()"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data zakończenia</label>
                        <input
                            type="datetime-local"
                            id="eventEnd"
                            class="form-input"
                            required
                            step="900"
                            min="{{ date('Y-m-d') }}T08:15"
                            onchange="validateTimeInputs()"
                        >
                    </div>
                </div>

                @if(Auth::user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fizjoterapeuta</label>
                    <select id="doctorId" class="form-input" required>
                        @foreach(App\Models\User::where('role', 'doctor')->get() as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->firstname }} {{ $doctor->lastname }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pacjent</label>
                    <select id="patientId" class="form-input">
                        <option value="">Wybierz pacjenta (opcjonalne)</option>
                        @foreach(App\Models\User::where('role', 'user')->get() as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->firstname }} {{ $patient->lastname }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="statusField" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status wizyty</label>
                    <select id="eventStatus" class="form-input">
                        <option value="scheduled">Zaplanowana</option>
                        <option value="completed">Zakończona</option>
                        <option value="cancelled">Anulowana</option>
                        <option value="no_show">Nieobecność</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1 text-green-600"></i>
                        Cena wizyty (opcjonalnie)
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="eventPrice"
                            class="form-input pl-10"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                        >
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">PLN</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pozostaw puste jeśli wizyta jest bezpłatna</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notatki</label>
                    <textarea id="eventNotes" class="form-input" rows="3" placeholder="Dodatkowe informacje o wizycie"></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="button" onclick="closeModal()" class="btn-secondary">
                        Anuluj
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Zapisz wizytę
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="eventDetails"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
let calendar;
let isMobile = window.innerWidth <= 768;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const modal = document.getElementById('eventModal');
    const eventForm = document.getElementById('eventForm');

    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }

    console.log('Initializing calendar for mobile:', isMobile);
    
    // Sprawdź czy harmonogram się zmienił od ostatniego odwiedzenia kalendarza
    const scheduleUpdated = localStorage.getItem('scheduleUpdated');
    const scheduleUpdatedAt = localStorage.getItem('scheduleUpdatedAt');
    if (scheduleUpdated === 'true' && scheduleUpdatedAt) {
        showNotification('Harmonogram zmienił się! Odświeżam dostępne sloty...', 'info');
        // Czyszczenie flagi
        localStorage.removeItem('scheduleUpdated');
        localStorage.removeItem('scheduleUpdatedAt');
    }

    // Flaga do kontrolowania auto-edit (tylko raz przy pierwszym załadowaniu)
    let autoEditProcessed = false;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'listWeek' : 'timeGridWeek',
        locale: 'pl',
        timeZone: 'Europe/Warsaw',
        height: '900px',
        contentHeight: 'auto',
        aspectRatio: 1.35,
        allDaySlot: false, // Wyłącz wiersz all-day
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        slotDuration: '00:30:00',
        expandRows: true,

        headerToolbar: {
            left: isMobile ? 'prev,next' : 'prev,next today',
            center: 'title',
            right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },

        buttonText: {
            today: 'Dzisiaj',
            month: 'Miesiąc',
            week: 'Tydzień',
            day: 'Dzień',
            list: 'Lista'
        },

        noEventsContent: 'Brak wydarzeń do wyświetlenia',

        firstDay: 1,
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '08:00',
            endTime: '20:00'
        },

        editable: '{{ Auth::user()->role }}' !== 'user',
        selectable: '{{ Auth::user()->role }}' !== 'user',
        selectMirror: true,
        dayMaxEvents: isMobile ? 3 : 4,
        moreLinkClick: 'popover',
        weekNumbers: false,
        nowIndicator: true,
        eventDisplay: 'block',

        // Lepsze ustawienia dla wydarzeń
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        // Lepsze wyświetlanie czasu
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        // Responsywne ustawienia
        contentHeight: isMobile ? 400 : 'auto',
        windowResizeDelay: 100,

        events: "/calendar/events",

        // Obsługa touch events dla mobile
        eventStartEditable: '{{ Auth::user()->role }}' === 'admin' || '{{ Auth::user()->role }}' === 'doctor',
        eventDurationEditable: '{{ Auth::user()->role }}' === 'admin' || '{{ Auth::user()->role }}' === 'doctor',

        select: function(info) {
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
            document.getElementById('eventStart').value = info.startStr;
            document.getElementById('eventEnd').value = info.endStr;
            openModal('addEvent');
            @endif
            calendar.unselect();
        },

        eventClick: function(info) {
            info.jsEvent.preventDefault();
            showEventDetails(info.event);
        },

        eventDrop: function(info) {
            if (checkTimeConflict(info.event)) {
                showNotification('W tym czasie już istnieje inna wizyta!', 'error');
                info.revert();
                return;
            }

            // Use startStr and endStr to avoid timezone conversion issues
            // Format: 2025-12-15T18:00:00+01:00 -> 2025-12-15T18:00
            const formattedStart = info.event.startStr.substring(0, 16);
            const formattedEnd = info.event.endStr.substring(0, 16);

            console.log('eventDrop - Sending to backend:', {
                startStr: info.event.startStr,
                endStr: info.event.endStr,
                formatted_start: formattedStart,
                formatted_end: formattedEnd
            });

            updateEventDateTime(info.event, 'move', {
                start: formattedStart,
                end: formattedEnd
            }, info.revert);
        },

        eventResize: function(info) {
            if (checkTimeConflict(info.event)) {
                showNotification('W tym czasie już istnieje inna wizyta!', 'error');
                info.revert();
                return;
            }

            // Use endStr to avoid timezone conversion issues
            // Format: 2025-12-15T18:15:00+01:00 -> 2025-12-15T18:15
            const formattedEnd = info.event.endStr.substring(0, 16);

            updateEventDateTime(info.event, 'resize', {
                end: formattedEnd
            }, info.revert);
        },
        viewDidMount: function(info) {
            // Przerysuj wydarzenia po zmianie widoku
            setTimeout(() => {
                const events = calendar.getEvents();
                events.forEach(event => {
                    const eventEl = event.el;
                    if (eventEl) {
                        const userRole = '{{ Auth::user()->role }}';
                        const formattedTitle = formatEventTitleForView(event, userRole, info.view.type);
                        const titleElement = eventEl.querySelector('.fc-event-title');
                        if (titleElement) {
                            titleElement.textContent = formattedTitle;
                        }

                        // Zastosuj specjalne formatowanie dla widoku czasowego
                        if (info.view.type === 'timeGridWeek' || info.view.type === 'timeGridDay') {
                            formatTimeGridEvent(eventEl, event, userRole);
                        }
                    }
                });
            }, 100);
        },

        eventDidMount: function(info) {
            const userRole = '{{ Auth::user()->role }}';
            const currentView = calendar.view.type;

            // Formatuj tytuł wydarzenia w zależności od widoku
            const formattedTitle = formatEventTitleForView(info.event, userRole, currentView);

            // Ustaw sformatowany tytuł
            const titleElement = info.el.querySelector('.fc-event-title');
            if (titleElement) {
                titleElement.textContent = formattedTitle;
            }

            // Dodaj tooltip z pełnymi informacjami
            const tooltipText = getFullEventTooltip(info.event);
            info.el.setAttribute('title', tooltipText);

            // Ustaw klasę CSS na podstawie typu
            if (info.event.extendedProps.type) {
                const eventType = info.event.extendedProps.type.toLowerCase()
                    .replace(/[ąęłńóśúżć]/g, function(match) {
                        const map = {'ą':'a','ę':'e','ł':'l','ń':'n','ó':'o','ś':'s','ú':'z','ż':'z','ć':'c'};
                        return map[match] || match;
                    })
                    .replace(/\s+/g, '');

                const typeMap = {
                    'fizjoterapia': 'fizjoterapia',
                    'konsultacja': 'konsultacja',
                    'masazleczniczy': 'masaz',
                    'masaz': 'masaz',
                    'neurorehabilitacja': 'neurorehabilitacja',
                    'wizytakontrolna': 'kontrola',
                    'kontrola': 'kontrola'
                };

                const cssClass = typeMap[eventType] || 'konsultacja';
                info.el.classList.add(cssClass);
            }

            // Sprawdź długość wydarzenia i dodaj odpowiednią klasę
            const duration = info.event.end ? (info.event.end - info.event.start) / (1000 * 60) : 30;
            if (duration <= 30) {
                info.el.classList.add('fc-event-short');
            }

            // Wyłącz drag & drop dla pacjentów
            if (userRole === 'user') {
                info.el.style.cursor = 'pointer';
                info.el.classList.add('patient-readonly');
            }

            // Dodaj informację o statusie
            if (info.event.extendedProps.status === 'cancelled') {
                info.el.style.opacity = '0.6';
                info.el.style.filter = 'grayscale(50%)';
                info.el.classList.add('status-cancelled');
            }

            // Podświetl dzisiejsze wydarzenia
            if (info.event.extendedProps.is_today) {
                info.el.style.boxShadow = '0 0 8px rgba(251, 191, 36, 0.6)';
                info.el.style.border = '1px solid #fbbf24';
                info.el.classList.add('today-event');
            }

            // Specjalne formatowanie dla widoku tygodniowego/dziennego
            if (currentView === 'timeGridWeek' || currentView === 'timeGridDay') {
                formatTimeGridEvent(info.el, info.event, userRole);
            }
        },

        windowResize: function() {
            const newIsMobile = window.innerWidth <= 768;
            if (newIsMobile !== isMobile) {
                isMobile = newIsMobile;
                updateCalendarForScreenSize();
            }
        },

        eventsSet: function(events) {
            // Auto-open edit form if URL contains edit parameter (tylko przy pierwszym załadowaniu)
            if (autoEditProcessed) {
                return; // Już przetworzono, nie rób tego ponownie
            }

            const urlParams = new URLSearchParams(window.location.search);
            const editEventId = urlParams.get('edit');

            if (editEventId && events.length > 0) {
                console.log('Auto-edit: Szukam wydarzenia o ID:', editEventId);
                console.log('Załadowane wydarzenia:', events.length);

                // Konwertuj ID do stringa dla pewności
                const event = calendar.getEventById(String(editEventId));

                if (event) {
                    console.log('Auto-edit: Znaleziono wydarzenie w cache:', event.title);
                    // Navigate to the event's date
                    calendar.gotoDate(event.start);
                    // Open edit form
                    editEvent(editEventId);
                    // Remove the parameter from URL
                    window.history.replaceState({}, document.title, window.location.pathname);
                    // Oznacz jako przetworzone
                    autoEditProcessed = true;
                } else {
                    console.log('Auto-edit: Wydarzenie nie w cache, pobieram z backendu...');
                    // FALLBACK: Pobierz wydarzenie bezpośrednio z API
                    fetch(`/calendar/${editEventId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Nie znaleziono wydarzenia');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Auto-edit: Pobrano z backendu:', data);
                            if (data.appointment) {
                                // Parsuj datę i przejdź do niej
                                const eventDate = new Date(data.appointment.start);
                                calendar.gotoDate(eventDate);

                                // Poczekaj aż kalendarz załaduje wydarzenia dla nowej daty
                                setTimeout(() => {
                                    const event = calendar.getEventById(String(editEventId));
                                    if (event) {
                                        console.log('Auto-edit: Wydarzenie załadowane po zmianie daty');
                                        editEvent(editEventId);
                                        window.history.replaceState({}, document.title, window.location.pathname);
                                    } else {
                                        console.error('Auto-edit: Nie udało się załadować wydarzenia');
                                        showNotification('Nie można otworzyć formularza edycji', 'error');
                                    }
                                }, 1000);
                            }
                        })
                        .catch(error => {
                            console.error('Auto-edit: Błąd pobierania wydarzenia:', error);
                            showNotification('Nie znaleziono wydarzenia o ID: ' + editEventId, 'error');
                        })
                        .finally(() => {
                            autoEditProcessed = true;
                        });
                }
            }
        }
    });

    try {
        console.log('Rendering calendar...');
        calendar.render();
        console.log('Calendar rendered successfully!');

        // NOWE: Jeśli harmonogram się zmienił - odśwież kalendarz
        if (scheduleUpdated === 'true') {
            // Czekaj 500ms aby calendar był w pełni wyrendering
            setTimeout(function() {
                calendar.refetchEvents();
                console.log('Odświeżono eventy z powodu zmiany harmonogramu');
            }, 500);
        }
    } catch (error) {
        console.error('Error rendering calendar:', error);
    }

    // Form submission
    eventForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitButton = e.target.querySelector('button[type="submit"]');
        submitButton.classList.add('loading');
        submitButton.disabled = true;

        try {
            const title = document.getElementById('eventTitle').value;
            const start = document.getElementById('eventStart').value;
            const end = document.getElementById('eventEnd').value;
            const type = document.getElementById('eventType').value;
            const editEventId = e.target.dataset.editEventId;

            // Walidacja czasu
            const timeValidation = validateEventTime(start, end);
            if (!timeValidation.valid) {
                showNotification(timeValidation.message, 'error');
                return;
            }

            const formData = {
                title: title,
                start: start,
                end: end,
                type: type,
                notes: document.getElementById('eventNotes').value,
                patient_id: document.getElementById('patientId').value || null,
                price: document.getElementById('eventPrice').value || null,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            if (editEventId) {
                formData.id = editEventId;
                // Add status only when editing
                const statusField = document.getElementById('eventStatus');
                if (statusField && statusField.parentElement.style.display !== 'none') {
                    formData.status = statusField.value;
                }
            }

            const doctorSelect = document.getElementById('doctorId');
            if (doctorSelect) {
                formData.doctor_id = doctorSelect.value;
            }

            const url = editEventId ? `/calendar/${editEventId}` : '/calendar/store';
            const response = await fetch(url, {
                method: editEventId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const responseData = await response.json();

            if (response.ok) {
                calendar.refetchEvents();
                closeModal();
                eventForm.reset();
                delete eventForm.dataset.editEventId;
                showNotification(
                    responseData.message || (editEventId ? 'Wizyta została zaktualizowana pomyślnie' : 'Wizyta została dodana pomyślnie'),
                    'success'
                );
            } else {
                showNotification('Błąd: ' + (responseData.error || responseData.message || 'Nie udało się zapisać wizyty'), 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Wystąpił błąd podczas zapisywania wizyty', 'error');
        } finally {
            submitButton.classList.remove('loading');
            submitButton.disabled = false;
        }
    });

    // Touch handling dla lepszej responsywności
    setupTouchHandling();

    // Ustaw ograniczenia czasu dla inputów
    setupTimeConstraints();
});

// === FUNKCJE FORMATOWANIA WYDARZEŃ ===
// Te funkcje odpowiadają za zwięzłe i czytelne wyświetlanie wydarzeń w kalendarzu

/**
 * Formatuj tytuł wydarzenia w zależności od widoku kalendarza
 * Zapewnia optymalne wykorzystanie miejsca w każdym widoku
 */
function formatEventTitleForView(event, userRole, viewType) {
    const startTime = event.start.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });

    switch(viewType) {
        case 'dayGridMonth':
            // Widok miesięczny - tylko godzina i nazwisko
            if (userRole === 'admin') {
                const patientName = event.extendedProps.patient_name ?
                    getLastName(event.extendedProps.patient_name) : 'Blok';
                return `${startTime} ${patientName}`;
            } else if (userRole === 'doctor') {
                const patientName = event.extendedProps.patient_name ?
                    getLastName(event.extendedProps.patient_name) : 'Blok';
                return `${startTime} ${patientName}`;
            } else {
                const doctorName = event.extendedProps.doctor_name ?
                    'Dr ' + getLastName(event.extendedProps.doctor_name) : 'Lekarz';
                return `${doctorName}`;
            }

        case 'timeGridWeek':
            // Widok tygodniowy - skrótowe info
            if (userRole === 'admin') {
                const patientName = event.extendedProps.patient_name ?
                    getShortName(event.extendedProps.patient_name) : 'Blokada';
                const doctorInitial = event.extendedProps.doctor_name ?
                    getInitials(event.extendedProps.doctor_name) : '??';
                return `${patientName}\n${doctorInitial}`;
            } else if (userRole === 'doctor') {
                const patientName = event.extendedProps.patient_name ?
                    getShortName(event.extendedProps.patient_name) : 'Blokada';
                return `${patientName}`;
            } else {
                const doctorName = event.extendedProps.doctor_name ?
                    'Dr ' + getLastName(event.extendedProps.doctor_name) : 'Lekarz';
                return `${doctorName}`;
            }

        case 'timeGridDay':
            // Widok dzienny - średnio szczegółowe
            if (userRole === 'admin') {
                const patientName = event.extendedProps.patient_name ?
                    getShortName(event.extendedProps.patient_name) : 'Blokada';
                const doctorName = event.extendedProps.doctor_name ?
                    'Dr ' + getLastName(event.extendedProps.doctor_name) : 'Dr ???';
                return `${patientName}\n${doctorName}`;
            } else if (userRole === 'doctor') {
                const patientName = event.extendedProps.patient_name ?
                    getShortName(event.extendedProps.patient_name) : 'Blokada';
                const typeShort = getShortType(event.extendedProps.type);
                return `${patientName}\n${typeShort}`;
            } else {
                const doctorName = event.extendedProps.doctor_name ?
                    'Dr ' + getShortName(event.extendedProps.doctor_name) : 'Lekarz';
                const typeShort = getShortType(event.extendedProps.type);
                return `${doctorName}\n${typeShort}`;
            }

        case 'listWeek':
        case 'listMonth':
            // Widok listy - pełne informacje
            return getFullEventTitle(event, userRole);

        default:
            return event.title || 'Wizyta';
    }
}

/**
 * Pobierz tylko nazwisko
 */
function getLastName(fullName) {
    if (!fullName) return '';
    const parts = fullName.trim().split(' ');
    return parts.length > 1 ? parts[parts.length - 1] : parts[0];
}

/**
 * Pobierz inicjały (imię + nazwisko)
 */
function getInitials(fullName) {
    if (!fullName) return '??';
    const parts = fullName.trim().split(' ');
    if (parts.length === 1) {
        return parts[0].charAt(0).toUpperCase() + '.';
    }
    return parts[0].charAt(0).toUpperCase() + parts[parts.length - 1].charAt(0).toUpperCase();
}

/**
 * Skróć nazwisko do inicjału (ulepszona wersja)
 */
function getShortName(fullName) {
    if (!fullName) return '';
    const parts = fullName.trim().split(' ');

    if (parts.length === 1) {
        // Tylko jedno słowo - skróć jeśli za długie
        return parts[0].length > 12 ? parts[0].substring(0, 10) + '..' : parts[0];
    } else {
        // Imię + inicjał nazwiska
        const firstName = parts[0].length > 8 ? parts[0].substring(0, 8) : parts[0];
        const lastName = parts[parts.length - 1];
        return `${firstName} ${lastName.charAt(0)}.`;
    }
}

/**
 * Dostosuj długość tekstu do dostępnego miejsca
 */
function adaptTextToSpace(text, maxLength = 20) {
    if (!text || text.length <= maxLength) {
        return text;
    }

    // Jeśli tekst ma więcej niż jedną linię (zawiera \n), skróć każdą linię osobno
    if (text.includes('\n')) {
        return text.split('\n')
            .map(line => line.length > maxLength ? line.substring(0, maxLength - 1) + '…' : line)
            .join('\n');
    }

    // Skróć tekst i dodaj wielokropek
    return text.substring(0, maxLength - 1) + '…';
}

/**
 * Sprawdź czy nazwa powinna być skrócona na podstawie kontekstu
 */
function shouldShortenName(viewType, duration) {
    if (viewType === 'dayGridMonth') return true;
    if (viewType === 'timeGridWeek' && duration <= 60) return true;
    if (viewType === 'timeGridDay' && duration <= 30) return true;
    return false;
}

/**
 * Pobierz skrócony typ zabiegu
 */
function getShortType(type) {
    const shortTypes = {
        'fizjoterapia': 'Fizjo',
        'konsultacja': 'Konsult.',
        'masaz': 'Masaż',
        'neurorehabilitacja': 'Neuro',
        'kontrola': 'Kontrola'
    };
    return shortTypes[type] || 'Wizyta';
}

// === KONIEC FUNKCJI FORMATOWANIA ===

/**
 * Pobierz pełny tytuł wydarzenia
 */
function getFullEventTitle(event, userRole) {
    const title = event.title || 'Wizyta';

    switch(userRole) {
        case 'admin':
            const patientName = event.extendedProps.patient_name || 'Blokada czasu';
            const doctorName = event.extendedProps.doctor_name ?
                'Dr ' + event.extendedProps.doctor_name : 'Dr ???';
            return `${title} - ${patientName} (${doctorName})`;

        case 'doctor':
            const patientForDoctor = event.extendedProps.patient_name || 'Blokada czasu';
            return `${title} - ${patientForDoctor}`;

        case 'user':
            const doctorForPatient = event.extendedProps.doctor_name ?
                'Dr ' + event.extendedProps.doctor_name : 'Dr ???';
            return `${title} - ${doctorForPatient}`;

        default:
            return title;
    }
}

/**
 * Pobierz pełny tooltip dla wydarzenia
 */
function getFullEventTooltip(event) {
    const startTime = event.start.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
    const endTime = event.end ? event.end.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' }) : '';
    const date = event.start.toLocaleDateString('pl-PL', { weekday: 'long', day: 'numeric', month: 'long' });

    const tooltipParts = [
        event.title || 'Wizyta',
        `Data: ${date}`,
        `Godzina: ${startTime}${endTime ? ` - ${endTime}` : ''}`,
    ];

    if (event.extendedProps.type_display) {
        tooltipParts.push(`Typ: ${event.extendedProps.type_display}`);
    }

    if (event.extendedProps.patient_name) {
        tooltipParts.push(`Pacjent: ${event.extendedProps.patient_name}`);
    }

    if (event.extendedProps.doctor_name) {
        tooltipParts.push(`Fizjoterapeuta: Dr ${event.extendedProps.doctor_name}`);
    }

    if (event.extendedProps.notes) {
        tooltipParts.push(`Notatki: ${event.extendedProps.notes}`);
    }

    return tooltipParts.join('\n');
}

/**
 * Specjalne formatowanie dla widoku czasowego
 */
function formatTimeGridEvent(eventElement, event, userRole) {
    const titleElement = eventElement.querySelector('.fc-event-title');
    if (!titleElement) return;

    // Sprawdź długość wydarzenia
    const duration = event.end ? (event.end - event.start) / (1000 * 60) : 30;

    if (duration <= 30) {
        // Bardzo krótkie wydarzenia (≤30min) - jedna linia, bardzo skrótowo
        titleElement.style.whiteSpace = 'nowrap';
        titleElement.style.textOverflow = 'ellipsis';
        titleElement.style.overflow = 'hidden';
        titleElement.style.lineHeight = '1';
        titleElement.style.fontSize = '10px';
        titleElement.style.fontWeight = '600';
        eventElement.style.padding = '1px 3px';

        const shortText = getUltraShortEventTitle(event, userRole);
        titleElement.textContent = shortText;

    } else if (duration <= 60) {
        // Średnie wydarzenia (30-60min) - maksymalnie dwie linie
        titleElement.style.whiteSpace = 'normal';
        titleElement.style.overflow = 'hidden';
        titleElement.style.lineHeight = '1.1';
        titleElement.style.fontSize = '11px';
        titleElement.style.fontWeight = '500';
        titleElement.style.display = '-webkit-box';
        titleElement.style.webkitLineClamp = '2';
        titleElement.style.webkitBoxOrient = 'vertical';
        eventElement.style.padding = '2px 4px';

    } else {
        // Długie wydarzenia (>60min) - maksymalnie trzy linie
        titleElement.style.whiteSpace = 'normal';
        titleElement.style.overflow = 'hidden';
        titleElement.style.lineHeight = '1.2';
        titleElement.style.fontSize = '11px';
        titleElement.style.fontWeight = '500';
        titleElement.style.display = '-webkit-box';
        titleElement.style.webkitLineClamp = '3';
        titleElement.style.webkitBoxOrient = 'vertical';
        eventElement.style.padding = '3px 5px';
    }

    // Dodatkowe style dla lepszej czytelności
    titleElement.style.wordBreak = 'break-word';
    titleElement.style.hyphens = 'auto';
}

/**
 * Pobierz ultra-krótki tytuł dla bardzo krótkich wydarzeń (≤30min)
 */
function getUltraShortEventTitle(event, userRole) {
    if (userRole === 'admin') {
        const patientName = event.extendedProps.patient_name ?
            getVeryShortName(event.extendedProps.patient_name) : 'Blok';
        return patientName;
    } else if (userRole === 'doctor') {
        const patientName = event.extendedProps.patient_name ?
            getVeryShortName(event.extendedProps.patient_name) : 'Blok';
        return patientName;
    } else {
        const doctorName = event.extendedProps.doctor_name ?
            getVeryShortName(event.extendedProps.doctor_name) : '???';
        return doctorName;
    }
}

/**
 * Bardzo krótkie nazwy dla małych wydarzeń (maksymalnie 6-8 znaków)
 */
function getVeryShortName(fullName) {
    if (!fullName) return '';

    const parts = fullName.trim().split(' ');
    if (parts.length === 1) {
        // Jedno słowo - skróć do max 6 znaków
        return parts[0].length > 6 ? parts[0].substring(0, 5) + '.' : parts[0];
    } else {
        // Wiele słów - pierwsze 3-4 znaki imienia + inicjał
        const firstName = parts[0];
        const lastName = parts[parts.length - 1];
        const shortFirst = firstName.length > 4 ? firstName.substring(0, 3) : firstName;
        return `${shortFirst}${lastName.charAt(0)}.`;
    }
}

// Walidacja czasu wizyty
function validateEventTime(startTime, endTime) {
    const start = new Date(startTime);
    const end = new Date(endTime);

    const startHour = start.getHours();
    const startMinute = start.getMinutes();
    const endHour = end.getHours();
    const endMinutes = end.getMinutes();
    const dayOfWeek = start.getDay(); // 0 = niedziela, 1 = poniedziałek, ..., 6 = sobota

    // Sprawdź czy niedziela (0)
    if (dayOfWeek === 0) {
        return {
            valid: false,
            message: 'Wizyty nie mogą być dodawane w niedzielę. Gabinet jest zamknięty.'
        };
    }

    // Poniedziałek-Piątek (1-5): 8:00-20:00
    if (dayOfWeek >= 1 && dayOfWeek <= 5) {
        // Sprawdź czas rozpoczęcia
        if (startHour < 8 || startHour >= 20) {
            return {
                valid: false,
                message: 'Poniedziałek-Piątek: Wizyty mogą być dodawane od 8:00 do 20:00'
            };
        }

        // Sprawdź czas zakończenia (musi być przed 20:00)
        if (endHour > 20 || (endHour === 20 && endMinutes > 0)) {
            return {
                valid: false,
                message: 'Wizyta musi się zakończyć najpóźniej o 20:00'
            };
        }
    }

    // Sobota (6): 9:00-15:00
    if (dayOfWeek === 6) {
        // Sprawdź czas rozpoczęcia
        if (startHour < 9 || startHour >= 15) {
            return {
                valid: false,
                message: 'Sobota: Wizyty mogą być dodawane od 9:00 do 15:00'
            };
        }

        // Sprawdź czas zakończenia (musi być przed 15:00)
        if (endHour > 15 || (endHour === 15 && endMinutes > 0)) {
            return {
                valid: false,
                message: 'Wizyta musi się zakończyć najpóźniej o 15:00'
            };
        }
    }

    // Sprawdź czy wizyta nie jest za krótka (minimum 15 minut)
    const duration = (end - start) / (1000 * 60); // w minutach
    if (duration < 15) {
        return {
            valid: false,
            message: 'Wizyta musi trwać co najmniej 15 minut'
        };
    }

    // Sprawdź czy wizyta nie jest za długa (maksimum 4 godziny)
    if (duration > 240) {
        return {
            valid: false,
            message: 'Wizyta nie może trwać dłużej niż 4 godziny'
        };
    }

    return { valid: true };
}

// Walidacja inputów czasu
function validateTimeInputs() {
    const startInput = document.getElementById('eventStart');
    const endInput = document.getElementById('eventEnd');

    if (startInput.value && endInput.value) {
        const startTime = new Date(startInput.value);
        const endTime = new Date(endInput.value);
        const dayOfWeek = startTime.getDay(); // 0 = niedziela, 1-5 = pon-pią, 6 = sobota

        // Określ godziny pracy na podstawie dnia tygodnia
        let minHour, maxHour;

        if (dayOfWeek === 0) {
            // Niedziela - zamknięte
            startInput.style.borderColor = '#ef4444';
            startInput.title = 'Gabinet jest zamknięty w niedzielę';
            endInput.style.borderColor = '#ef4444';
            endInput.title = 'Gabinet jest zamknięty w niedzielę';
            return;
        } else if (dayOfWeek >= 1 && dayOfWeek <= 5) {
            // Poniedziałek-Piątek: 8:00-20:00
            minHour = 8;
            maxHour = 20;
        } else if (dayOfWeek === 6) {
            // Sobota: 9:00-15:00
            minHour = 9;
            maxHour = 15;
        }

        // Normalny styl
        startInput.style.borderColor = '';
        startInput.title = '';
        endInput.style.borderColor = '';
        endInput.title = '';

        const startHour = startTime.getHours();
        const endHour = endTime.getHours();
        const endMinute = endTime.getMinutes();

        // Automatyczna korekcja czasu rozpoczęcia
        if (startHour < minHour) {
            startTime.setHours(minHour, 0);
            startInput.value = formatDateTimeForInput(startTime);
        } else if (startHour >= maxHour) {
            startTime.setHours(maxHour - 1, 0);
            startInput.value = formatDateTimeForInput(startTime);
        }

        // Automatyczna korekcja czasu zakończenia
        if (endHour < minHour) {
            endTime.setHours(minHour, 30);
            endInput.value = formatDateTimeForInput(endTime);
        } else if (endHour > maxHour || (endHour === maxHour && endMinute > 0)) {
            endTime.setHours(maxHour, 0);
            endInput.value = formatDateTimeForInput(endTime);
        }

        // Ustaw minimum dla pola końcowego na podstawie pola początkowego
        const newStart = new Date(startInput.value);
        const minEnd = new Date(newStart.getTime() + 15 * 60 * 1000); // +15 minut
        endInput.min = formatDateTimeForInput(minEnd);

        // Jeśli czas końcowy jest wcześniejszy niż początkowy + 15 minut, skoryguj go
        if (new Date(endInput.value) < minEnd) {
            endInput.value = formatDateTimeForInput(minEnd);
        }
    }
}

// Ustaw ograniczenia czasu dla inputów
function setupTimeConstraints() {
    const startInput = document.getElementById('eventStart');
    const endInput = document.getElementById('eventEnd');

    if (startInput && endInput) {
        startInput.addEventListener('change', function() {
            validateTimeInputs();
            updateTimeConstraintsForDay();
        });
        endInput.addEventListener('change', validateTimeInputs);

        // Ustaw ograniczenia czasu dla dzisiejszej daty
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];

        startInput.min = `${todayStr}T08:00`;
        endInput.min = `${todayStr}T08:15`;

        // Ustaw ograniczenie maksymalne (np. 1 rok w przód)
        const nextYear = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());
        const nextYearStr = nextYear.toISOString().split('T')[0];

        startInput.max = `${nextYearStr}T20:00`;
        endInput.max = `${nextYearStr}T20:00`;

        // Inicjalnie zaktualizuj ograniczenia
        updateTimeConstraintsForDay();
    }
}

// Zaktualizuj ograniczenia czasu na podstawie wybranego dnia
function updateTimeConstraintsForDay() {
    const startInput = document.getElementById('eventStart');
    if (!startInput.value) return;

    const selectedDate = new Date(startInput.value);
    const dayOfWeek = selectedDate.getDay(); // 0 = niedziela, 1-5 = pon-pią, 6 = sobota
    const dateStr = selectedDate.toISOString().split('T')[0];

    let minTime, maxTime;

    if (dayOfWeek === 0) {
        // Niedziela - zamknięte
        minTime = '08:00';
        maxTime = '08:00';
        startInput.style.borderColor = '#ef4444';
    } else if (dayOfWeek >= 1 && dayOfWeek <= 5) {
        // Poniedziałek-Piątek: 8:00-20:00
        minTime = '08:00';
        maxTime = '20:00';
        startInput.style.borderColor = '';
    } else if (dayOfWeek === 6) {
        // Sobota: 9:00-15:00
        minTime = '09:00';
        maxTime = '15:00';
        startInput.style.borderColor = '';
    }

    startInput.min = `${dateStr}T${minTime}`;
    startInput.max = `${dateStr}T${maxTime}`;
}

// Update calendar settings for screen size
function updateCalendarForScreenSize() {
    // Zaktualizuj toolbar
    calendar.setOption('headerToolbar', {
        left: isMobile ? 'prev,next' : 'prev,next today',
        center: 'title',
        right: isMobile ? 'dayGridMonth,timeGridDay' : 'dayGridMonth,timeGridWeek,timeGridDay'
    });

    // Zaktualizuj liczbę widocznych wydarzeń
    calendar.setOption('dayMaxEvents', isMobile ? 3 : 4);
    calendar.setOption('aspectRatio', isMobile ? 1.0 : 1.35);

    // Przełącz na widok miesięczny na mobile jeśli był widok tygodniowy
    if (isMobile && calendar.view.type === 'timeGridWeek') {
        calendar.changeView('dayGridMonth');
    }

    // Odśwież wyświetlanie
    setTimeout(() => {
        calendar.render();
    }, 100);
}

// Touch handling setup
function setupTouchHandling() {
    let touchStartTime = 0;
    let touchStartPos = { x: 0, y: 0 };

    document.addEventListener('touchstart', function(e) {
        touchStartTime = Date.now();
        if (e.touches[0]) {
            touchStartPos.x = e.touches[0].clientX;
            touchStartPos.y = e.touches[0].clientY;
        }
    }, { passive: true });

    document.addEventListener('touchend', function(e) {
        const touchEndTime = Date.now();
        const touchDuration = touchEndTime - touchStartTime;

        // Detect tap vs long press
        if (touchDuration < 200) {
            // Quick tap - normal behavior
        } else if (touchDuration > 500) {
            // Long press - could be used for additional actions
        }
    }, { passive: true });
}

// Modal functions
function openModal(type) {
    const modal = document.getElementById('eventModal');
    const modalTitle = modal.querySelector('h2');

    if (type === 'addEvent') {
        modalTitle.textContent = 'Dodaj nową wizytę';
        const submitButton = document.querySelector('#eventForm button[type="submit"]');
        submitButton.innerHTML = '<i class="fas fa-save"></i>Zapisz wizytę';
        delete document.getElementById('eventForm').dataset.editEventId;
        document.getElementById('eventForm').reset();

        // Hide status field when adding new event
        const statusField = document.getElementById('statusField');
        if (statusField) {
            statusField.style.display = 'none';
        }
    }

    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scroll

    // Ustaw domyślną datę w zakresie 8:00-20:00
    if (!document.getElementById('eventStart').value) {
        const now = new Date();
        let startTime = new Date(now.getTime() + (30 - now.getMinutes() % 30) * 60000);

        // Upewnij się, że godzina jest w zakresie 8:00-20:00
        if (startTime.getHours() < 8) {
            startTime.setHours(8, 0, 0, 0);
        } else if (startTime.getHours() >= 20) {
            // Jeśli po 20:00, ustaw na 8:00 następnego dnia
            startTime.setDate(startTime.getDate() + 1);
            startTime.setHours(8, 0, 0, 0);
        }

        const endTime = new Date(startTime.getTime() + 60 * 60 * 1000); // +1 hour

        // Upewnij się, że czas końcowy nie przekracza 20:00
        if (endTime.getHours() > 20) {
            endTime.setHours(20, 0, 0, 0);
        }

        document.getElementById('eventStart').value = formatDateTimeForInput(startTime);
        document.getElementById('eventEnd').value = formatDateTimeForInput(endTime);
    }

    // Focus first input on desktop
    if (!isMobile) {
        setTimeout(() => {
            document.getElementById('eventTitle').focus();
        }, 100);
    }
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
    document.getElementById('eventDetailsModal').style.display = 'none';
    document.getElementById('eventForm').reset();
    delete document.getElementById('eventForm').dataset.editEventId;
    document.body.style.overflow = ''; // Restore scroll
}

function showEventDetails(event) {
    const modal = document.getElementById('eventDetailsModal');
    const detailsDiv = document.getElementById('eventDetails');

    // Use startStr/endStr to avoid timezone conversion issues
    // Format: 2025-12-15T14:30:00+01:00 -> extract time 14:30
    const startStr = event.startStr || event.start.toISOString();
    const endStr = event.endStr || (event.end ? event.end.toISOString() : '');
    const startTime = startStr.substring(11, 16);  // HH:mm
    const endTime = endStr ? endStr.substring(11, 16) : '';  // HH:mm

    // For date, we can use event.start since it's just for display
    const dateFormatter = new Intl.DateTimeFormat('pl-PL', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Europe/Warsaw'
    });
    const date = dateFormatter.format(event.start);

    const userRole = '{{ Auth::user()->role }}';
    const canEdit = userRole === 'admin' || userRole === 'doctor';

    detailsDiv.innerHTML = `
        <h2 class="text-xl font-bold text-gray-900 mb-6 pr-12">${event.title}</h2>
        <div class="space-y-4">
            <div class="flex items-start">
                <i class="fas fa-calendar-alt text-indigo-600 w-6 mr-3 mt-1 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Data i godzina</p>
                    <p class="text-gray-600">${date}</p>
                    <p class="text-gray-600">${startTime} - ${endTime}</p>
                </div>
            </div>
            ${event.extendedProps.type ? `
            <div class="flex items-start">
                <i class="fas fa-stethoscope text-indigo-600 w-6 mr-3 mt-1 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Typ terapii</p>
                    <p class="text-gray-600">${event.extendedProps.type}</p>
                </div>
            </div>` : ''}
            ${event.extendedProps.patient_name ? `
            <div class="flex items-start">
                <i class="fas fa-user text-indigo-600 w-6 mr-3 mt-1 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Pacjent</p>
                    <p class="text-gray-600">${event.extendedProps.patient_name}</p>
                </div>
            </div>` : ''}
            ${event.extendedProps.doctor_name ? `
            <div class="flex items-start">
                <i class="fas fa-user-md text-indigo-600 w-6 mr-3 mt-1 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Fizjoterapeuta</p>
                    <p class="text-gray-600">${event.extendedProps.doctor_name}</p>
                </div>
            </div>` : ''}
            ${event.extendedProps.notes ? `
            <div class="flex items-start">
                <i class="fas fa-notes-medical text-indigo-600 w-6 mr-3 mt-1 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Notatki</p>
                    <p class="text-gray-600">${event.extendedProps.notes}</p>
                </div>
            </div>` : ''}
        </div>
        ${canEdit ? `
        <div class="modal-buttons">
            <button onclick="deleteEvent('${event.id}')" class="btn-danger">
                <i class="fas fa-trash"></i>
                Usuń
            </button>
            <button onclick="editEvent('${event.id}')" class="btn-primary">
                <i class="fas fa-edit"></i>
                Edytuj
            </button>
        </div>
        ` : ''}
    `;

    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Quick navigation functions
function showToday() {
    calendar.today();
    if (isMobile) {
        calendar.changeView('timeGridDay');
    }
}

function showWeek() {
    calendar.changeView(isMobile ? 'timeGridDay' : 'timeGridWeek');
}

function showMonth() {
    calendar.changeView('dayGridMonth');
}

// Edit event function
function editEvent(eventId) {
    const event = calendar.getEventById(eventId);
    if (!event) {
        console.error('Event not found:', eventId);
        return;
    }

    document.querySelector('#eventModal h2').textContent = 'Edytuj wizytę';
    document.getElementById('eventTitle').value = event.title || '';
    document.getElementById('eventType').value = event.extendedProps.type || 'fizjoterapia';

    // Use startStr and endStr to avoid timezone conversion issues
    // Format: 2025-01-08T14:00:00+01:00 -> 2025-01-08T14:00
    const startStr = event.startStr || event.start.toISOString();
    const endStr = event.endStr || event.end.toISOString();

    document.getElementById('eventStart').value = startStr.substring(0, 16);
    document.getElementById('eventEnd').value = endStr.substring(0, 16);
    document.getElementById('eventNotes').value = event.extendedProps.notes || '';
    document.getElementById('eventPrice').value = event.extendedProps.price || '';

    if (document.getElementById('patientId')) {
        document.getElementById('patientId').value = event.extendedProps.patient_id || '';
    }
    if (document.getElementById('doctorId')) {
        document.getElementById('doctorId').value = event.extendedProps.doctor_id || '';
    }

    // Show and set status field
    const statusField = document.getElementById('statusField');
    const eventStatus = document.getElementById('eventStatus');
    if (statusField && eventStatus) {
        statusField.style.display = 'block';
        eventStatus.value = event.extendedProps.status || 'scheduled';
    }

    const submitButton = document.querySelector('#eventForm button[type="submit"]');
    submitButton.innerHTML = '<i class="fas fa-save"></i>Zapisz zmiany';

    const form = document.getElementById('eventForm');
    form.dataset.editEventId = eventId;

    document.getElementById('eventDetailsModal').style.display = 'none';
    openModal('editEvent');
}

// Delete event function - with custom modal
function deleteEvent(eventId) {
    showDeleteEventModal(eventId);
}

// Custom modal functions for delete event
let deleteEventCallback = null;

function showDeleteEventModal(eventId) {
    const modal = document.getElementById('deleteEventModal');
    if (!modal) {
        // Modal doesn't exist yet, create it
        createDeleteEventModal();
        setTimeout(() => showDeleteEventModal(eventId), 100);
        return;
    }

    deleteEventCallback = async function() {
        try {
            const response = await fetch(`/calendar/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const responseData = await response.json();

            if (response.ok) {
                const eventToRemove = calendar.getEventById(eventId);
                if (eventToRemove) {
                    eventToRemove.remove();
                }
                closeDeleteEventModal();
                closeModal();
                showNotification(responseData.message || 'Wizyta została usunięta pomyślnie', 'success');
            } else {
                showNotification('Błąd: ' + (responseData.error || responseData.message || 'Nie udało się usunąć wizyty'), 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Wystąpił błąd podczas usuwania wizyty', 'error');
        }
    };

    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

function closeDeleteEventModal() {
    const modal = document.getElementById('deleteEventModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
    deleteEventCallback = null;
}

function confirmDeleteEvent() {
    if (deleteEventCallback) {
        deleteEventCallback();
    }
}

function createDeleteEventModal() {
    const modalHTML = `
    <div id="deleteEventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[2000]" style="display: none;">
        <div class="modal-content rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
            <div class="modal-header px-6 py-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="modal-title text-lg font-semibold">Usuń wizytę</h3>
                    <button onclick="closeDeleteEventModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4">
                <div class="flex items-start space-x-4">
                    <div class="shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                        <i class="fas fa-trash text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="modal-message text-sm">Czy na pewno chcesz usunąć tę wizytę? Ta operacja jest nieodwracalna.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-6 py-4 border-t flex justify-end space-x-3">
                <button onclick="closeDeleteEventModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">Anuluj</button>
                <button onclick="confirmDeleteEvent()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors"><i class="fas fa-trash mr-2"></i>Usuń</button>
            </div>
        </div>
    </div>
    <style>
        #deleteEventModal { backdrop-filter: blur(4px); }
        #deleteEventModal .modal-content { background-color: #ffffff; border: 1px solid #e5e7eb; }
        #deleteEventModal .modal-header { border-bottom-color: #e5e7eb; }
        #deleteEventModal .modal-footer { border-top-color: #e5e7eb; }
        #deleteEventModal .modal-title { color: #111827; }
        #deleteEventModal .modal-message { color: #6b7280; }
        body.dark-mode #deleteEventModal .modal-content { background-color: #1f2937; border-color: #374151; }
        body.dark-mode #deleteEventModal .modal-header { border-bottom-color: #374151; }
        body.dark-mode #deleteEventModal .modal-footer { border-top-color: #374151; }
        body.dark-mode #deleteEventModal .modal-title { color: #f9fafb; }
        body.dark-mode #deleteEventModal .modal-message { color: #d1d5db; }
    </style>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Add event listeners
    document.getElementById('deleteEventModal')?.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteEventModal();
        }
    });

    document.getElementById('deleteEventModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteEventModal();
        }
    });
}

// Update event date/time function
// Helper function to format date for backend (Y-m-d\TH:i format)
function formatDateTimeForBackend(date) {
    // NAPRAWIONE: Konwertuj do Europe/Warsaw timezone
    // Używamy toLocaleString z locale 'pl-PL' i options aby uniknąć problemu z timezone
    // Ale to nie zadziała prawidłowo dla wszystkich przeglądarek
    // Zamiast tego używamy prostego podejścia: wysłanie ISO8601 i let backend parsuje

    // Najsimplejsza metoda: ISO8601 string z FullCalendar zawsze ma właściwy timestamp
    // Wysyłamy ISO8601 bezpośrednio
    const isoString = date.toISOString();

    // Konwertuj ISO (UTC) do Warsaw timezone
    // UTC timestamp: 2025-12-15T16:00:00Z (16:00 UTC = 17:00 Warsaw)
    // Musimy dodać 1 godzinę dla CET lub 2 dla CEST
    // Ale to jest skomplikowane - lepiej wysłać ISO i let backend się zajmie

    // Actually - backend oczekuje YYYY-MM-DDTHH:mm w Europe/Warsaw
    // Możemy użyć Intl formatter z locale i options
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Europe/Warsaw'
    };

    const formatter = new Intl.DateTimeFormat('pl-PL', options);
    const parts = formatter.formatToParts(date);

    let year = '', month = '', day = '', hours = '', minutes = '';
    for (const part of parts) {
        if (part.type === 'year') year = part.value;
        if (part.type === 'month') month = String(part.value).padStart(2, '0');
        if (part.type === 'day') day = String(part.value).padStart(2, '0');
        if (part.type === 'hour') hours = String(part.value).padStart(2, '0');
        if (part.type === 'minute') minutes = String(part.value).padStart(2, '0');
    }

    console.log('formatDateTimeForBackend:', { input: date, year, month, day, hours, minutes });
    const result = `${year}-${month}-${day}T${hours}:${minutes}`;
    console.log('formatDateTimeForBackend result:', result);
    return result;
}

async function updateEventDateTime(event, action, data, revertFunc) {
    const user = '{{ Auth::user()->role }}';

    if (user !== 'admin' && user !== 'doctor') {
        showNotification('Nie masz uprawnień do przenoszenia wizyt', 'error');
        if (revertFunc) revertFunc();
        return;
    }

    try {
        const url = action === 'resize' ? `/calendar/${event.id}/resize` : `/calendar/${event.id}/move`;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const responseData = await response.json();

        if (response.ok) {
            showNotification(
                responseData.message || `Wizyta została ${action === 'resize' ? 'zaktualizowana' : 'przeniesiona'} pomyślnie`,
                'success'
            );
            // Odśwież wydarzenia żeby zobaczyć najnowsze dane z serwera
            calendar.refetchEvents();
        } else {
            showNotification('Błąd: ' + (responseData.error || responseData.message || 'Nie udało się zaktualizować wizyty'), 'error');
            if (revertFunc) revertFunc();
            calendar.refetchEvents();
        }
    } catch (error) {
        console.error('Error updating event:', error);
        showNotification('Wystąpił błąd podczas aktualizacji wizyty', 'error');
        if (revertFunc) revertFunc();
        calendar.refetchEvents();
    }
}

// Check time conflict function
function checkTimeConflict(movedEvent) {
    const doctorId = movedEvent.extendedProps.doctor_id;
    const movedStart = new Date(movedEvent.start);
    const movedEnd = new Date(movedEvent.end);

    const allEvents = calendar.getEvents();

    for (let event of allEvents) {
        if (event.id === movedEvent.id || event.extendedProps.doctor_id !== doctorId) {
            continue;
        }

        if (event.extendedProps.status === 'cancelled') {
            continue;
        }

        const eventStart = new Date(event.start);
        const eventEnd = new Date(event.end);

        if (movedStart < eventEnd && movedEnd > eventStart) {
            return true;
        }
    }

    return false;
}

// Notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-triangle',
        'warning': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
    };

    notification.innerHTML = `
        <button class="close-btn" onclick="closeNotification(this)" title="Zamknij">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex items-start">
            <i class="fas ${icons[type] || icons.info} mr-3 flex-shrink-0 mt-0.5 text-lg"></i>
            <span class="font-medium text-sm leading-relaxed">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Auto-hide only for success and info (not for errors and warnings)
    if (type === 'success' || type === 'info') {
        const duration = type === 'success' ? 4000 : 5000;
        setTimeout(() => {
            closeNotificationElement(notification);
        }, duration);
    }
    // Errors and warnings stay until user closes them
}

// Close notification by button click
function closeNotification(button) {
    const notification = button.closest('.notification');
    closeNotificationElement(notification);
}

// Close notification element
function closeNotificationElement(notification) {
    if (notification && document.body.contains(notification)) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }
}

// Utility functions
function formatDateTimeForInput(date) {
    return date.getFullYear().toString() + '-' +
           (date.getMonth() + 1).toString().padStart(2, '0') + '-' +
           date.getDate().toString().padStart(2, '0') + 'T' +
           date.getHours().toString().padStart(2, '0') + ':' +
           date.getMinutes().toString().padStart(2, '0');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const eventModal = document.getElementById('eventModal');
    const detailsModal = document.getElementById('eventDetailsModal');
    if (event.target === eventModal || event.target === detailsModal) {
        closeModal();
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Handle orientation change
window.addEventListener('orientationchange', function() {
    setTimeout(() => {
        calendar.updateSize();
    }, 500);
});

// Handle resize
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        const newIsMobile = window.innerWidth <= 768;
        if (newIsMobile !== isMobile) {
            isMobile = newIsMobile;
            updateCalendarForScreenSize();
        }
    }, 250);
});

// Prevent zoom on double tap (iOS Safari)
let lastTouchEnd = 0;
document.addEventListener('touchend', function (event) {
    const now = (new Date()).getTime();
    if (now - lastTouchEnd <= 300) {
        event.preventDefault();
    }
    lastTouchEnd = now;
}, false);

// Auto refresh calendar every 5 minutes when page is visible
setInterval(function() {
    if (document.visibilityState === 'visible') {
        calendar.refetchEvents();
    }
}, 300000);

console.log('Calendar script loaded successfully');

// Debug function - sprawdź czy wydarzenia są widoczne
function debugEvents() {
    const events = calendar.getEvents();
    const currentView = calendar.view.type;

    console.log('=== CALENDAR DEBUG ===');
    console.log('Current view:', currentView);
    console.log('Is mobile:', isMobile);
    console.log('Total events:', events.length);
    console.log('Calendar container height:', document.getElementById('calendar').offsetHeight);

    events.forEach((event, index) => {
        const element = event.el;
        console.log(`Event ${index + 1}:`, {
            title: event.title,
            start: event.start,
            visible: element ? element.offsetHeight > 0 : 'no element',
            styles: element ? {
                display: element.style.display,
                visibility: element.style.visibility,
                height: element.offsetHeight + 'px',
                width: element.offsetWidth + 'px'
            } : 'no element'
        });
    });
}

// Dodaj do window żeby można było wywołać z konsoli
window.debugEvents = debugEvents;
</script>
@endsection

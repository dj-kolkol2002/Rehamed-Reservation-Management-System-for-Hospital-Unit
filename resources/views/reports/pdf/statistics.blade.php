<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statystyki Systemu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e5e5;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .header p {
            color: #6b7280;
            margin-bottom: 5px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .stats-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .stats-table td {
            text-align: center;
            padding: 15px 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            width: 33.33%;
            vertical-align: top;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            display: block;
        }

        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
        }

        .stat-description {
            font-size: 9px;
            color: #10b981;
            display: block;
            margin-top: 5px;
            margin-top: 2px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-card {
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .chart-card h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .chart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .chart-label {
            display: flex;
            align-items: center;
            font-size: 11px;
            color: #374151;
        }

        .color-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .chart-value {
            font-size: 11px;
            font-weight: 500;
            color: #1f2937;
        }

        .chart-percentage {
            font-size: 10px;
            color: #6b7280;
            margin-left: 4px;
        }

        .activity-section {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .activity-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .activity-month {
            font-size: 11px;
            color: #374151;
        }

        .activity-count {
            font-size: 11px;
            font-weight: 500;
            color: #1f2937;
        }

        .activity-bar {
            width: 100px;
            height: 6px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-left: 10px;
        }

        .activity-progress {
            height: 100%;
            background-color: #8b5cf6;
            border-radius: 3px;
        }

        .popular-types {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .popular-types h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .type-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .type-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .type-bar {
            flex: 1;
            height: 6px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin: 0 10px;
        }

        .type-progress {
            height: 100%;
            border-radius: 3px;
        }

        .type-count {
            font-size: 11px;
            font-weight: 500;
            color: #1f2937;
            min-width: 80px;
            text-align: right;
        }

        .summary {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .summary h3 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #8b5cf6;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 10px;
            color: #6b7280;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Statystyki Systemu</h1>
        <p>Wygenerowano: {{ now()->format('d.m.Y H:i') }}</p>
        <p>Okres analizy: {{ request('date_from', now()->subMonths(6)->format('d.m.Y')) }} - {{ request('date_to', now()->format('d.m.Y')) }}</p>
        <p>System Medyczny - Zaawansowane Statystyki</p>
    </div>

    <div class="section">
        <h2 class="section-title">Statystyki użytkowników</h2>
        <table class="stats-table">
            <tr>
                <td>
                    <span class="stat-value">{{ number_format($userStats['total_users']) }}</span>
                    <span class="stat-label">Łączna liczba</span>
                    <span class="stat-description">{{ $userStats['active_users'] }} aktywnych</span>
                </td>
                <td>
                    <span class="stat-value">{{ number_format($userStats['doctors']) }}</span>
                    <span class="stat-label">Doktorzy</span>
                </td>
                <td>
                    <span class="stat-value">{{ number_format($userStats['patients']) }}</span>
                    <span class="stat-label">Pacjenci</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="stat-value">{{ number_format($userStats['admins']) }}</span>
                    <span class="stat-label">Administratorzy</span>
                </td>
                <td>
                    <span class="stat-value">{{ round(($userStats['active_users'] / max($userStats['total_users'], 1)) * 100, 1) }}%</span>
                    <span class="stat-label">Aktywność</span>
                </td>
                <td>
                    <span class="stat-value">{{ number_format($userStats['new_users_this_month']) }}</span>
                    <span class="stat-label">Nowi w tym miesiącu</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Statystyki dokumentów</h2>
        <table class="stats-table">
            <tr>
                <td>
                    <span class="stat-value">{{ number_format($documentStats['total_documents']) }}</span>
                    <span class="stat-label">Łączna liczba</span>
                </td>
                <td>
                    <span class="stat-value">{{ number_format($documentStats['by_status']['completed'] ?? 0) }}</span>
                    <span class="stat-label">Ukończone</span>
                </td>
                <td>
                    <span class="stat-value">{{ number_format($documentStats['by_status']['draft'] ?? 0) }}</span>
                    <span class="stat-label">Szkice</span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class="stat-value">{{ number_format($documentStats['documents_period']) }}</span>
                    <span class="stat-label">W analizowanym okresie</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3>Dokumenty według typu</h3>
            @php
                $typeLabels = [
                    'general' => 'Ogólne',
                    'diagnosis' => 'Diagnozy',
                    'treatment' => 'Leczenie',
                    'examination' => 'Badania',
                    'prescription' => 'Recepty'
                ];
                $typeColors = ['#6b7280', '#dc2626', '#2563eb', '#d97706', '#047857'];
                $totalByType = $documentStats['by_type']->sum();
            @endphp

            @foreach($typeLabels as $type => $label)
                @php
                    $count = $documentStats['by_type'][$type] ?? 0;
                    $percentage = $totalByType > 0 ? round(($count / $totalByType) * 100, 1) : 0;
                    $color = $typeColors[array_search($type, array_keys($typeLabels))] ?? '#6b7280';
                @endphp
                <div class="chart-item">
                    <div class="chart-label">
                        <div class="color-dot" style="background-color: {{ $color }};"></div>
                        {{ $label }}
                    </div>
                    <div class="chart-value">
                        {{ $count }}
                        <span class="chart-percentage">({{ $percentage }}%)</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="chart-card">
            <h3>Dokumenty według statusu</h3>
            @php
                $statusLabels = [
                    'completed' => 'Ukończone',
                    'draft' => 'Szkice',
                    'archived' => 'Zarchiwizowane'
                ];
                $statusColors = ['#047857', '#d97706', '#6b7280'];
                $totalByStatus = $documentStats['by_status']->sum();
            @endphp

            @foreach($statusLabels as $status => $label)
                @php
                    $count = $documentStats['by_status'][$status] ?? 0;
                    $percentage = $totalByStatus > 0 ? round(($count / $totalByStatus) * 100, 1) : 0;
                    $color = $statusColors[array_search($status, array_keys($statusLabels))] ?? '#6b7280';
                @endphp
                <div class="chart-item">
                    <div class="chart-label">
                        <div class="color-dot" style="background-color: {{ $color }};"></div>
                        {{ $label }}
                    </div>
                    <div class="chart-value">
                        {{ $count }}
                        <span class="chart-percentage">({{ $percentage }}%)</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="activity-section">
        <h3>Aktywność miesięczna (dokumenty)</h3>
        @foreach($monthlyActivity as $activity)
            @php
                $monthName = [
                    1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
                    5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
                    9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
                ][$activity->month] ?? '';
                $maxCount = $monthlyActivity->max('count');
                $percentage = $maxCount > 0 ? ($activity->count / $maxCount) * 100 : 0;
            @endphp
            <div class="activity-item">
                <span class="activity-month">{{ $monthName }} {{ $activity->year }}</span>
                <div class="activity-bar">
                    <div class="activity-progress" style="width: {{ $percentage }}%;"></div>
                </div>
                <span class="activity-count">{{ $activity->count }} dokumentów</span>
            </div>
        @endforeach
    </div>

    <div class="popular-types">
        <h3>Najpopularniejsze typy dokumentów w analizowanym okresie</h3>
        @foreach($popularTypes as $index => $typeData)
            @php
                $typeLabels = [
                    'general' => 'Ogólne',
                    'diagnosis' => 'Diagnozy',
                    'treatment' => 'Leczenie',
                    'examination' => 'Badania',
                    'prescription' => 'Recepty'
                ];
                $colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'];
                $color = $colors[$index] ?? '#6b7280';
                $label = $typeLabels[$typeData->type] ?? $typeData->type;
                $maxCount = $popularTypes->first()->count ?? 1;
                $percentage = ($typeData->count / $maxCount) * 100;
            @endphp
            <div class="type-item">
                <div class="type-info">
                    <div class="color-dot" style="background-color: {{ $color }};"></div>
                    <span style="font-size: 11px; color: #374151;">{{ $label }}</span>
                </div>
                <div class="type-bar">
                    <div class="type-progress" style="width: {{ $percentage }}%; background-color: {{ $color }};"></div>
                </div>
                <div class="type-count">{{ $typeData->count }} dokumentów</div>
            </div>
        @endforeach
    </div>

    <div class="summary">
        <h3>Podsumowanie statystyk</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($userStats['total_users']) }}</div>
                <div class="summary-label">Łączna liczba użytkowników</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($documentStats['total_documents']) }}</div>
                <div class="summary-label">Łączna liczba dokumentów</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ round(($userStats['active_users'] / max($userStats['total_users'], 1)) * 100, 1) }}%</div>
                <div class="summary-label">Aktywność użytkowników</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ round($documentStats['total_documents'] / max($userStats['doctors'], 1), 1) }}</div>
                <div class="summary-label">Dokumentów na doktora</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>System Medyczny - Raport wygenerowany automatycznie</p>
        <p>Strona 1 z 1</p>
    </div>
</body>
</html>

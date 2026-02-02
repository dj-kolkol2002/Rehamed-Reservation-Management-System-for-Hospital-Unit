<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Dokumentów Medycznych</title>
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

        .stats-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .stats-table td {
            text-align: center;
            padding: 15px 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            width: 25%;
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

        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
        }

        .filters h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .filters p {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        th {
            background-color: #f9fafb;
            font-weight: bold;
            font-size: 11px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            font-size: 11px;
        }

        .document-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .document-content {
            color: #6b7280;
            font-size: 10px;
        }

        .patient-name {
            font-weight: bold;
            color: #1f2937;
        }

        .doctor-name {
            color: #374151;
        }

        .type-general { background-color: #f3f4f6; color: #374151; }
        .type-diagnosis { background-color: #fee2e2; color: #dc2626; }
        .type-treatment { background-color: #dbeafe; color: #2563eb; }
        .type-examination { background-color: #fef3c7; color: #d97706; }
        .type-prescription { background-color: #d1fae5; color: #047857; }

        .status-draft { background-color: #fef3c7; color: #d97706; }
        .status-completed { background-color: #d1fae5; color: #047857; }
        .status-archived { background-color: #f3f4f6; color: #374151; }

        .type-badge, .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
            white-space: nowrap;
        }

        .has-file {
            color: #10b981;
            text-align: center;
        }

        .no-file {
            color: #d1d5db;
            text-align: center;
        }

        .charts-section {
            margin: 30px 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
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
            color: #10b981;
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
        <h1>Raport Dokumentów Medycznych</h1>
        <p>Wygenerowano: {{ now()->format('d.m.Y H:i') }}</p>
        <p>System Medyczny - Analiza Dokumentacji</p>
    </div>

    @if(!empty(array_filter($filters)))
        <div class="filters">
            <h3>Zastosowane filtry:</h3>
            @if($filters['type'] ?? false)
                <p><strong>Typ:</strong> {{ ['general' => 'Ogólny', 'diagnosis' => 'Diagnoza', 'treatment' => 'Leczenie', 'examination' => 'Badanie', 'prescription' => 'Recepta'][$filters['type']] }}</p>
            @endif
            @if($filters['status'] ?? false)
                <p><strong>Status:</strong> {{ ['draft' => 'Szkic', 'completed' => 'Ukończony', 'archived' => 'Zarchiwizowany'][$filters['status']] }}</p>
            @endif
            @if($filters['date_from'] ?? false)
                <p><strong>Data od:</strong> {{ $filters['date_from'] }}</p>
            @endif
            @if($filters['date_to'] ?? false)
                <p><strong>Data do:</strong> {{ $filters['date_to'] }}</p>
            @endif
        </div>
    @endif

    <table class="stats-table">
        <tr>
            <td>
                <span class="stat-value">{{ number_format($stats['total_documents']) }}</span>
                <span class="stat-label">Łącznie dokumentów</span>
            </td>
            <td>
                <span class="stat-value">{{ number_format($stats['by_status']['completed'] ?? 0) }}</span>
                <span class="stat-label">Ukończone</span>
            </td>
            <td>
                <span class="stat-value">{{ number_format($stats['by_status']['draft'] ?? 0) }}</span>
                <span class="stat-label">Szkice</span>
            </td>
            <td>
                <span class="stat-value">{{ number_format($stats['with_files']) }}</span>
                <span class="stat-label">Z załącznikami</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Tytuł dokumentu</th>
                <th>Pacjent</th>
                <th>Doktor</th>
                <th>Typ</th>
                <th>Status</th>
                <th>Data</th>
                <th>Plik</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $document)
                <tr>
                    <td>
                        <div class="document-title">{{ $document->title }}</div>
                        @if($document->content)
                            <div class="document-content">{{ Str::limit($document->content, 80) }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="patient-name">{{ $document->patient->full_name }}</div>
                    </td>
                    <td>
                        <div class="doctor-name">{{ $document->doctor->full_name }}</div>
                    </td>
                    <td>
                        <span class="type-badge type-{{ $document->type }}">
                            {{ $document->type_display }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $document->status }}">
                            {{ $document->status_display }}
                        </span>
                    </td>
                    <td>{{ $document->document_date->format('d.m.Y') }}</td>
                    <td>
                        @if($document->hasFile())
                            <div class="has-file">✓</div>
                        @else
                            <div class="no-file">-</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280;">Brak dokumentów do wyświetlenia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="charts-section">
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
            @endphp

            @foreach($typeLabels as $type => $label)
                @php
                    $count = $stats['by_type'][$type] ?? 0;
                    $percentage = $stats['total_documents'] > 0 ? round(($count / $stats['total_documents']) * 100, 1) : 0;
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
            @endphp

            @foreach($statusLabels as $status => $label)
                @php
                    $count = $stats['by_status'][$status] ?? 0;
                    $percentage = $stats['total_documents'] > 0 ? round(($count / $stats['total_documents']) * 100, 1) : 0;
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

    <div class="summary">
        <h3>Podsumowanie raportu</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['total_documents']) }}</div>
                <div class="summary-label">Łączna liczba dokumentów</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['by_status']['completed'] ?? 0) }}</div>
                <div class="summary-label">Dokumenty ukończone</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['by_status']['draft'] ?? 0) }}</div>
                <div class="summary-label">Dokumenty w szkicach</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['with_files']) }}</div>
                <div class="summary-label">Z załącznikami</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>System Medyczny - Raport wygenerowany automatycznie</p>
        <p>Strona 1 z 1</p>
    </div>
</body>
</html>

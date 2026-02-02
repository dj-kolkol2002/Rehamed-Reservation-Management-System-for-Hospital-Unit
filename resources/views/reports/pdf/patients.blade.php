<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Pacjentów</title>
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

        .patient-name {
            font-weight: bold;
            color: #1f2937;
        }

        .patient-id {
            color: #6b7280;
            font-size: 10px;
        }

        .status-active {
            background-color: #d1fae5;
            color: #047857;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
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
            color: #3b82f6;
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
        <h1>Raport Pacjentów</h1>
        <p>Wygenerowano: {{ now()->format('d.m.Y H:i') }}</p>
        <p>System Medyczny - Dokumentacja Pacjentów</p>
    </div>

    @if(!empty(array_filter($filters)))
        <div class="filters">
            <h3>Zastosowane filtry:</h3>
            @if($filters['status'] ?? false)
                <p><strong>Status:</strong> {{ $filters['status'] == 'active' ? 'Aktywni' : 'Nieaktywni' }}</p>
            @endif
            @if($filters['gender'] ?? false)
                <p><strong>Płeć:</strong> {{ ['male' => 'Mężczyzna', 'female' => 'Kobieta', 'other' => 'Inna'][$filters['gender']] }}</p>
            @endif
            @if($filters['date_from'] ?? false)
                <p><strong>Rejestracja od:</strong> {{ $filters['date_from'] }}</p>
            @endif
            @if($filters['date_to'] ?? false)
                <p><strong>Rejestracja do:</strong> {{ $filters['date_to'] }}</p>
            @endif
        </div>
    @endif

    <table class="stats-table">
        <tr>
            <td>
                <span class="stat-value">{{ number_format($stats['total_patients']) }}</span>
                <span class="stat-label">Wszyscy pacjenci</span>
            </td>
            <td>
                <span class="stat-value">{{ number_format($stats['active_patients']) }}</span>
                <span class="stat-label">Aktywni pacjenci</span>
            </td>
            <td>
                <span class="stat-value">{{ number_format($stats['with_documents']) }}</span>
                <span class="stat-label">Z dokumentacją</span>
            </td>
            <td>
                <span class="stat-value">{{ round($stats['average_age'] ?? 0) }}</span>
                <span class="stat-label">Średni wiek (lata)</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Pacjent</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Wiek</th>
                <th>Płeć</th>
                <th>Dokumenty</th>
                <th>Status</th>
                <th>Data rejestracji</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patients as $patient)
                <tr>
                    <td>
                        <div class="patient-name">{{ $patient->full_name }}</div>
                        <div class="patient-id">ID: {{ $patient->id }}</div>
                    </td>
                    <td>{{ $patient->email }}</td>
                    <td>{{ $patient->phone ?? '-' }}</td>
                    <td>
                        @if($patient->date_of_birth)
                            {{ $patient->date_of_birth->age }} lat
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($patient->gender)
                            {{ ['male' => 'M', 'female' => 'K', 'other' => 'I'][$patient->gender] ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $patient->patientDocuments->count() }}</td>
                    <td>
                        @if($patient->is_active)
                            <span class="status-active">Aktywny</span>
                        @else
                            <span class="status-inactive">Nieaktywny</span>
                        @endif
                    </td>
                    <td>{{ $patient->created_at->format('d.m.Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #6b7280;">Brak pacjentów do wyświetlenia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Podsumowanie raportu</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['total_patients']) }}</div>
                <div class="summary-label">Łączna liczba pacjentów</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['active_patients']) }}</div>
                <div class="summary-label">Aktywni pacjenci</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($stats['with_documents']) }}</div>
                <div class="summary-label">Z dokumentacją medyczną</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ round($stats['average_age'] ?? 0) }}</div>
                <div class="summary-label">Średni wiek (lata)</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>System Medyczny - Raport wygenerowany automatycznie</p>
        <p>Strona 1 z 1</p>
    </div>
</body>
</html>

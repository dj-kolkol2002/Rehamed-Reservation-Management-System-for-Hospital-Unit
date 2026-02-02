<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Faktura {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #3b82f6;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header .invoice-number {
            font-size: 14px;
            color: #666;
        }
        .row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .col-half {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        .info-box {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .info-box h3 {
            font-size: 12px;
            color: #3b82f6;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-box p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table thead {
            background-color: #3b82f6;
            color: white;
        }
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table tbody tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totals table {
            margin: 0;
        }
        .totals .total-row {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 13px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-issued {
            background: #dbeafe;
            color: #1e40af;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>FAKTURA</h1>
            <div class="invoice-number">Nr: {{ $invoice->invoice_number }}</div>
        </div>

        <!-- Company and Patient Info -->
        <div class="row">
            <div class="col-half">
                <div class="info-box">
                    <h3>Sprzedawca</h3>
                    <p><strong>{{ $company['name'] }}</strong></p>
                    @if(!empty($company['nip']))
                        <p>NIP: {{ $company['nip'] }}</p>
                    @endif
                    @if(!empty($company['address']))
                        <p>{{ $company['address'] }}</p>
                    @endif
                    @if(!empty($company['city']) && !empty($company['postal_code']))
                        <p>{{ $company['postal_code'] }} {{ $company['city'] }}</p>
                    @endif
                    @if(!empty($company['phone']))
                        <p>Tel: {{ $company['phone'] }}</p>
                    @endif
                    @if(!empty($company['email']))
                        <p>Email: {{ $company['email'] }}</p>
                    @endif
                </div>
            </div>

            <div class="col-half">
                <div class="info-box">
                    <h3>Nabywca</h3>
                    <p><strong>{{ $invoice->user->firstname }} {{ $invoice->user->lastname }}</strong></p>
                    @if($invoice->user->address)
                        <p>{{ $invoice->user->address }}</p>
                    @endif
                    @if($invoice->user->phone)
                        <p>Tel: {{ $invoice->user->phone }}</p>
                    @endif
                    <p>Email: {{ $invoice->user->email }}</p>
                </div>

                <div class="info-box">
                    <h3>Dane faktury</h3>
                    <p><strong>Data wystawienia:</strong> {{ $invoice->issued_at ? $invoice->issued_at->format('d.m.Y') : '-' }}</p>
                    <p><strong>Data płatności:</strong> {{ $invoice->paid_at ? $invoice->paid_at->format('d.m.Y') : '-' }}</p>
                    <p><strong>Status:</strong>
                        @if($invoice->isPaid())
                            <span class="status-badge status-paid">OPŁACONA</span>
                        @else
                            <span class="status-badge status-issued">WYSTAWIONA</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Lp.</th>
                    <th>Usługa</th>
                    <th style="width: 100px;" class="text-right">Ilość</th>
                    <th style="width: 120px;" class="text-right">Cena jedn.</th>
                    <th style="width: 120px;" class="text-right">Wartość</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>{{ $invoice->payment->appointment->title }}</strong><br>
                        <small>{{ $invoice->payment->appointment->type_display }}</small><br>
                        <small>Data wizyty: {{ $invoice->payment->appointment->start_time->format('d.m.Y H:i') }}</small>
                        @if($invoice->payment->appointment->doctor)
                            <br><small>Fizjoterapeuta: {{ $invoice->payment->appointment->doctor->firstname }} {{ $invoice->payment->appointment->doctor->lastname }}</small>
                        @endif
                    </td>
                    <td class="text-right">1</td>
                    <td class="text-right">{{ number_format($invoice->amount, 2, ',', ' ') }} {{ strtoupper($invoice->currency) }}</td>
                    <td class="text-right">{{ number_format($invoice->amount, 2, ',', ' ') }} {{ strtoupper($invoice->currency) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="clearfix">
            <div class="totals">
                <table>
                    <tr>
                        <td><strong>Razem netto:</strong></td>
                        <td class="text-right">{{ number_format($invoice->amount, 2, ',', ' ') }} {{ strtoupper($invoice->currency) }}</td>
                    </tr>
                    <tr>
                        <td><strong>VAT (zwolnione):</strong></td>
                        <td class="text-right">0,00 {{ strtoupper($invoice->currency) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>RAZEM DO ZAPŁATY:</strong></td>
                        <td class="text-right"><strong>{{ number_format($invoice->amount, 2, ',', ' ') }} {{ strtoupper($invoice->currency) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment Method -->
        <div style="clear: both; margin-top: 30px;">
            <p><strong>Forma płatności:</strong>
                @if($invoice->payment->payment_method === 'cash')
                    Gotówka
                @elseif($invoice->payment->payment_method === 'stripe')
                    Płatność online (karta/BLIK)
                @elseif($invoice->payment->payment_method === 'card')
                    Karta płatnicza
                @elseif($invoice->payment->payment_method === 'transfer')
                    Przelew bankowy
                @else
                    {{ ucfirst($invoice->payment->payment_method) }}
                @endif
            </p>
        </div>

        @if($invoice->notes)
            <div style="margin-top: 20px;">
                <p><strong>Uwagi:</strong></p>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Dziękujemy za skorzystanie z naszych usług!</p>
            <p>{{ $company['name'] }} | {{ $company['email'] }} | {{ $company['phone'] ?? '' }}</p>
        </div>
    </div>
</body>
</html>

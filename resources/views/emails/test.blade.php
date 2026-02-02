<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rehamed - Test Wysyłania Email</h1>
    </div>
    <div class="content">
        <h2>Witaj!</h2>
        <p>To jest testowy email z systemu Rehamed.</p>
        <p>Jeśli widzisz ten email w Mailtrapa, oznacza to, że:</p>
        <ul>
            <li>✅ Konfiguracja SMTP działa poprawnie</li>
            <li>✅ Laravel może wysyłać emaile</li>
            <li>✅ Mailtrap przechwytuje wiadomości</li>
        </ul>
        <p><strong>Czas wysłania:</strong> {{ now()->format('d.m.Y H:i:s') }}</p>
        <p><strong>Środowisko:</strong> {{ config('app.env') }}</p>
    </div>
    <div class="footer">
        <p>To jest automatyczna wiadomość testowa z systemu Rehamed</p>
        <p>&copy; {{ date('Y') }} Rehamed - System Zarządzania Kliniką</p>
    </div>
</body>
</html>

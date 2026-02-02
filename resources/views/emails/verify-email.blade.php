{{-- resources/views/emails/verify-email.blade.php --}}
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdź swój adres email - Rehamed</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header .logo {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #4b5563;
        }
        .verification-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .verification-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .info-box {
            background-color: #f3f4f6;
            border-left: 4px solid #fbbf24;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .security-note {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
        }
        .alternative-link {
            word-break: break-all;
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #6b7280;
            margin-top: 15px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .verification-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">💊</div>
            <h1>Rehamed</h1>
            <p>Klinika Rehabilitacji</p>
        </div>

        <div class="content">
            <div class="greeting">
                Witaj {{ $user->firstname }}!
            </div>

            <div class="message">
                Dziękujemy za rejestrację w klinice Rehamed. Jesteśmy podekscytowani możliwością pomocy w Twojej drodze do zdrowia i dobrego samopoczucia.
            </div>

            <div class="message">
                Aby dokończyć proces rejestracji i aktywować swoje konto, potwierdź swój adres email klikając poniższy przycisk:
            </div>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verification-button">
                    ✉️ Potwierdź adres email
                </a>
            </div>

            <div class="info-box">
                <strong>📝 Ważne informacje:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Ten link wygaśnie za <strong>60 minut</strong></li>
                    <li>Po weryfikacji będziesz mógł w pełni korzystać z naszego systemu</li>
                    <li>Będziesz mógł umówić wizyty, przeglądać dokumentację medyczną i kontaktować się z lekarzami</li>
                </ul>
            </div>

            <div class="security-note">
                <strong>🔒 Bezpieczeństwo:</strong> Jeśli nie zakładałeś konta w klinice Rehamed, zignoruj tę wiadomość. Twoje dane są bezpieczne i nikt nie uzyska dostępu do Twojego adresu email.
            </div>

            <div class="message">
                Jeśli przycisk nie działa, możesz skopiować poniższy link i wkleić go w przeglądarce:
            </div>

            <div class="alternative-link">
                {{ $verificationUrl }}
            </div>
        </div>

        <div class="footer">
            <p><strong>Zespół Rehamed</strong></p>
            <p>ul. Zdrowotna 123, 42-310 Żarki</p>
            <p>📞 +48 123 456 789 | ✉️ kontakt@rehamed.pl</p>
            <p style="margin-top: 15px; font-size: 12px;">
                Ta wiadomość została wysłana automatycznie. Prosimy nie odpowiadać na ten email.
            </p>
            <p style="margin-top: 10px; font-size: 12px;">
                © {{ date('Y') }} Rehamed. Wszystkie prawa zastrzeżone.
            </p>
        </div>
    </div>
</body>
</html>

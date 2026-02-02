# REHAMED - System Zarządzania Rezerwacjami dla Oddziału Szpitalnego

System do zarządzania wizytami, harmonogramami lekarzy, płatnościami i dokumentacją medyczną w klinice fizjoterapii. Zbudowany w Laravel 12 z interaktywnym kalendarzem, czatem wewnętrznym i integracją Stripe.

## Funkcjonalności

- **Zarządzanie wizytami** - interaktywny kalendarz (FullCalendar), rezerwacja, przesuwanie wizyt drag & drop, wykrywanie konfliktów
- **3 role użytkowników** - Administrator, Fizjoterapeuta (lekarz), Pacjent
- **System płatności** - Stripe (karta, BLIK, Przelewy24), płatności gotówkowe, automatyczne generowanie faktur PDF
- **Dokumentacja medyczna** - tworzenie dokumentów przez lekarzy, generowanie PDF, kontrola dostępu
- **Komunikator** - wewnętrzny czat z załącznikami plików
- **Powiadomienia** - w aplikacji i email
- **Raporty** - statystyki wizyt, zarobków, płatności
- **Harmonogramy lekarzy** - zarządzanie dostępnością, godziny pracy, blokowanie slotów

## Wymagania

- PHP 8.2+
- MySQL 8.0+ / MariaDB
- Composer
- Node.js & NPM
- Stripe account (dla płatności online)

## Instalacja

```bash
# Klonowanie repozytorium
git clone https://github.com/dj-kolkol2002/Rehamed-Reservation-Management-System-for-Hospital-Unit.git
cd Rehamed-Reservation-Management-System-for-Hospital-Unit

# Instalacja zależności
composer install
npm install

# Konfiguracja środowiska
cp .env.example .env
php artisan key:generate
```

Edytuj `.env` i ustaw dane bazy danych oraz klucze Stripe:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rehamed
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
```

```bash
# Migracja bazy danych i seedowanie
php artisan migrate
php artisan db:seed

# Symlink storage
php artisan storage:link

# Build frontendu
npm run build
```

## Uruchomienie

```bash
# Tryb deweloperski (serwer + Vite + kolejka + logi)
composer dev

# Lub ręcznie:
php artisan serve
npm run dev
```

Aplikacja będzie dostępna pod `http://localhost:8000`.

## Stack technologiczny

| Warstwa | Technologia |
|---------|-------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, TailwindCSS 4, Alpine.js |
| Kalendarz | FullCalendar v6 |
| Płatności | Stripe PHP SDK |
| PDF | DomPDF |
| Baza danych | MySQL / MariaDB |
| Build | Vite 6 |

## Struktura projektu

```
app/
├── Http/Controllers/    # Kontrolery (kalendarz, rezerwacje, płatności, czat, raporty)
├── Models/              # Modele Eloquent (User, Appointment, Payment, Invoice...)
├── Services/            # Logika biznesowa (AvailabilityService, NotificationService)
├── Mail/                # Szablony email
└── Notifications/       # Powiadomienia
resources/views/         # Szablony Blade
database/migrations/     # Migracje bazy danych
routes/web.php           # Routing aplikacji
```

## Licencja

MIT

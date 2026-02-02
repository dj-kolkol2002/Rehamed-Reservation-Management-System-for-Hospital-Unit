# REHAMED - Reservation Management System for Hospital Unit

A management system for appointments, doctor schedules, payments, and medical documentation in a physiotherapy clinic. Built with Laravel 12, featuring an interactive calendar, internal chat, and Stripe integration.

## Features

- **Appointment Management** - interactive calendar (FullCalendar), booking, drag & drop rescheduling, conflict detection
- **3 User Roles** - Administrator, Physiotherapist (doctor), Patient
- **Payment System** - Stripe (card, BLIK, Przelewy24), cash payments, automatic PDF invoice generation
- **Medical Documentation** - document creation by doctors, PDF generation, access control
- **Internal Chat** - messaging system with file attachments
- **Notifications** - in-app and email notifications
- **Reports** - appointment statistics, earnings, payments
- **Doctor Schedules** - availability management, working hours, slot blocking

## Requirements

- PHP 8.2+
- MySQL 8.0+ / MariaDB
- Composer
- Node.js & NPM
- Stripe account (for online payments)

## Installation

```bash
# Clone the repository
git clone https://github.com/dj-kolkol2002/Rehamed-Reservation-Management-System-for-Hospital-Unit.git
cd Rehamed-Reservation-Management-System-for-Hospital-Unit

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials and Stripe keys:

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
# Run migrations and seed the database
php artisan migrate
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Build frontend assets
npm run build
```

## Running the Application

```bash
# Development mode (server + Vite + queue + logs)
composer dev

# Or manually:
php artisan serve
npm run dev
```

The application will be available at `http://localhost:8000`.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, TailwindCSS 4, Alpine.js |
| Calendar | FullCalendar v6 |
| Payments | Stripe PHP SDK |
| PDF | DomPDF |
| Database | MySQL / MariaDB |
| Build | Vite 6 |

## Project Structure

```
app/
├── Http/Controllers/    # Controllers (calendar, reservations, payments, chat, reports)
├── Models/              # Eloquent models (User, Appointment, Payment, Invoice...)
├── Services/            # Business logic (AvailabilityService, NotificationService)
├── Mail/                # Email templates
└── Notifications/       # Notification classes
resources/views/         # Blade templates
database/migrations/     # Database migrations
routes/web.php           # Application routing
```

## License

MIT

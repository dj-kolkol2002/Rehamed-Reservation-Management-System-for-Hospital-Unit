<?php
// database/seeders/UserSeeder.php (bezpieczna wersja)

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting User Seeder...');

        // Sprawdź strukturę tabeli przed rozpoczęciem
        $this->checkTableStructure();

        // Lista użytkowników do utworzenia/aktualizacji
        $users = [
            // Administrator
            [
                'firstname' => 'Admin',
                'lastname' => 'Rehamed',
                'email' => 'admin@rehamed.pl',
                'password' => 'password123',
                'role' => 'admin',
                'phone' => '+48 123 456 789',
                'is_active' => true,
            ],

            // Fizjoterapeuci
            [
                'firstname' => 'Anna',
                'lastname' => 'Kowalska',
                'email' => 'anna.kowalska@rehamed.pl',
                'password' => 'password123',
                'role' => 'doctor',
                'phone' => '+48 234 567 890',
                'address' => 'ul. Medyczna 15, 40-000 Katowice',
                'date_of_birth' => '1985-03-15',
                'gender' => 'female',
                'is_active' => true,
            ],

            [
                'firstname' => 'Piotr',
                'lastname' => 'Nowak',
                'email' => 'piotr.nowak@rehamed.pl',
                'password' => 'password123',
                'role' => 'doctor',
                'phone' => '+48 345 678 901',
                'address' => 'ul. Rehabilitacyjna 22, 40-000 Katowice',
                'date_of_birth' => '1980-07-20',
                'gender' => 'male',
                'is_active' => true,
            ],

            [
                'firstname' => 'Marta',
                'lastname' => 'Wiśniewska',
                'email' => 'marta.wisniewska@rehamed.pl',
                'password' => 'password123',
                'role' => 'doctor',
                'phone' => '+48 456 789 012',
                'address' => 'ul. Fizjoterapii 8, 40-000 Katowice',
                'date_of_birth' => '1990-11-10',
                'gender' => 'female',
                'is_active' => true,
            ],

            // Pacjenci
            [
                'firstname' => 'Jan',
                'lastname' => 'Kowalczyk',
                'email' => 'jan.kowalczyk@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '+48 567 890 123',
                'address' => 'ul. Pacjentów 12, 40-000 Katowice',
                'date_of_birth' => '1975-05-25',
                'gender' => 'male',
                'emergency_contact' => 'Maria Kowalczyk, +48 678 901 234',
                'medical_history' => [
                    'Uraz kolana (2020)',
                    'Dyskopatia L5-S1',
                    'Alergia na ibuprofen'
                ],
                'is_active' => true,
            ],

            [
                'firstname' => 'Barbara',
                'lastname' => 'Nowak',
                'email' => 'barbara.nowak@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '+48 678 901 234',
                'address' => 'ul. Zdrowia 45, 40-000 Katowice',
                'date_of_birth' => '1982-09-12',
                'gender' => 'female',
                'emergency_contact' => 'Andrzej Nowak, +48 789 012 345',
                'medical_history' => [
                    'Fibromialgia',
                    'Migrena przewlekła',
                    'Stan po złamaniu nadgarstka (2019)'
                ],
                'is_active' => true,
            ],

            [
                'firstname' => 'Tomasz',
                'lastname' => 'Wiśniewski',
                'email' => 'tomasz.wisniewski@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '+48 789 012 345',
                'address' => 'ul. Rehabilitacji 33, 40-000 Katowice',
                'date_of_birth' => '1965-12-03',
                'gender' => 'male',
                'emergency_contact' => 'Krystyna Wiśniewska, +48 890 123 456',
                'medical_history' => [
                    'Choroba Parkinsona',
                    'Nadciśnienie tętnicze',
                    'Zaburzenia równowagi'
                ],
                'is_active' => true,
            ],

            [
                'firstname' => 'Agnieszka',
                'lastname' => 'Kaczmarek',
                'email' => 'agnieszka.kaczmarek@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '+48 890 123 456',
                'address' => 'ul. Młodych 67, 40-000 Katowice',
                'date_of_birth' => '1995-04-18',
                'gender' => 'female',
                'emergency_contact' => 'Paweł Kaczmarek, +48 901 234 567',
                'medical_history' => [
                    'Skolioza',
                    'Zespół cieśni nadgarstka',
                    'Przewlekły ból kręgosłupa'
                ],
                'is_active' => true,
            ],

            [
                'firstname' => 'Marek',
                'lastname' => 'Zieliński',
                'email' => 'marek.zielinski@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '+48 901 234 567',
                'address' => 'ul. Sportowa 89, 40-000 Katowice',
                'date_of_birth' => '1988-08-07',
                'gender' => 'male',
                'emergency_contact' => 'Ewa Zielińska, +48 012 345 678',
                'medical_history' => [
                    'Uszkodzenie więzadeł krzyżowych (2021)',
                    'Kontuzja ramienia',
                    'Zapalenie ścięgna Achillesa'
                ],
                'is_active' => true,
            ],

            // Nieaktywny użytkownik
            [
                'firstname' => 'Inactive',
                'lastname' => 'User',
                'email' => 'inactive@example.com',
                'password' => 'password123',
                'role' => 'user',
                'phone' => '+48 000 000 000',
                'is_active' => false,
            ],
        ];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($users as $userData) {
            $result = $this->createOrUpdateUser($userData);

            switch ($result) {
                case 'created':
                    $created++;
                    break;
                case 'updated':
                    $updated++;
                    break;
                case 'skipped':
                    $skipped++;
                    break;
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Seeding completed!");
        $this->command->line("📊 Summary:");
        $this->command->line("  🆕 Created: {$created} users");
        $this->command->line("  🔄 Updated: {$updated} users");
        $this->command->line("  ⏭️  Skipped: {$skipped} users");
        $this->command->line("  📈 Total in database: " . User::count() . " users");

        // Pokaż statystyki według ról
        $this->showRoleStatistics();
    }

    /**
     * Sprawdź strukturę tabeli i wyświetl informacje
     */
    private function checkTableStructure()
    {
        try {
            $columns = Schema::getColumnListing('users');
            $this->command->line("🔍 Available columns: " . implode(', ', $columns));

            // Sprawdź czy kluczowe kolumny istnieją
            $requiredColumns = ['firstname', 'lastname', 'role'];
            $missingColumns = array_diff($requiredColumns, $columns);

            if (!empty($missingColumns)) {
                $this->command->warn('⚠️  Missing required columns: ' . implode(', ', $missingColumns));
                $this->command->warn('Please run: php artisan migrate');
            }
        } catch (\Exception $e) {
            $this->command->error('Error checking table structure: ' . $e->getMessage());
        }
    }

    /**
     * Utwórz lub zaktualizuj użytkownika
     */
    private function createOrUpdateUser(array $userData): string
    {
        try {
            $email = $userData['email'];

            // Sprawdź czy użytkownik już istnieje
            $existingUser = User::where('email', $email)->first();

            // Przygotuj dane
            $userData['email_verified_at'] = now();
            $userData['password'] = Hash::make($userData['password']);

            // Usuń kolumny które nie istnieją w tabeli
            $columns = Schema::getColumnListing('users');
            $userData = array_filter($userData, function($key) use ($columns) {
                return in_array($key, $columns);
            }, ARRAY_FILTER_USE_KEY);

            if ($existingUser) {
                // Sprawdź czy trzeba zaktualizować
                $needsUpdate = false;

                // Sprawdź kilka kluczowych pól
                if (isset($userData['firstname']) && $existingUser->firstname !== $userData['firstname']) {
                    $needsUpdate = true;
                }
                if (isset($userData['role']) && $existingUser->role !== $userData['role']) {
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    // Nie aktualizuj hasła jeśli użytkownik już istnieje
                    unset($userData['password']);

                    $existingUser->update($userData);
                    $this->command->line("🔄 Updated: {$email}");
                    return 'updated';
                } else {
                    $this->command->line("⏭️  Skipped: {$email} (already exists and up to date)");
                    return 'skipped';
                }
            } else {
                // Utwórz nowego użytkownika
                $user = User::create($userData);
                $name = ($user->firstname ?? 'User') . ' ' . ($user->lastname ?? '');
                $this->command->line("🆕 Created: {$name} ({$email})");
                return 'created';
            }

        } catch (\Exception $e) {
            $this->command->error("❌ Error with {$userData['email']}: " . $e->getMessage());
            return 'skipped';
        }
    }

    /**
     * Pokaż statystyki według ról
     */
    private function showRoleStatistics()
    {
        try {
            $this->command->newLine();
            $this->command->line("👥 Users by role:");

            $adminCount = User::where('role', 'admin')->count();
            $doctorCount = User::where('role', 'doctor')->count();
            $patientCount = User::where('role', 'user')->count();

            $this->command->line("  👑 Administrators: {$adminCount}");
            $this->command->line("  👨‍⚕️ Doctors: {$doctorCount}");
            $this->command->line("  🏥 Patients: {$patientCount}");

            $activeCount = User::where('is_active', true)->count();
            $inactiveCount = User::where('is_active', false)->count();

            $this->command->line("  ✅ Active: {$activeCount}");
            $this->command->line("  ❌ Inactive: {$inactiveCount}");

            // Sprawdź czy istnieje konto administratora
            $adminUser = User::where('email', 'admin@rehamed.pl')->first();
            if ($adminUser) {
                $this->command->newLine();
                $this->command->info("🔑 Admin login credentials:");
                $this->command->line("   Email: admin@rehamed.pl");
                $this->command->line("   Password: password123");
                $this->command->line("   URL: http://localhost:8000/admin/users");
            }

        } catch (\Exception $e) {
            $this->command->error('Error showing statistics: ' . $e->getMessage());
        }
    }
}

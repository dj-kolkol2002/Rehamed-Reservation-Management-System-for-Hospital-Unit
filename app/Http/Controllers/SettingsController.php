<?php
// app/Http/Controllers/SettingsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\MedicalDocument;
use App\Models\Appointment;
use App\Models\ChatMessage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display settings page
     */
    public function index()
    {
        $user = Auth::user();

        // Pobierz aktualny motyw (domyślnie light)
        $currentTheme = $user->preferences['theme'] ?? 'light';

        return view('settings.index', compact('currentTheme'));
    }

    /**
     * Update theme
     */
    public function updateTheme(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'theme' => 'required|in:light,dark',
        ], [
            'theme.required' => 'Motyw jest wymagany.',
            'theme.in' => 'Wybierz prawidłowy motyw.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Aktualizuj preferencje
        $preferences = $user->preferences ?? [];
        $preferences['theme'] = $request->theme;

        $user->update(['preferences' => $preferences]);

        return redirect()->route('settings.index')
            ->with('success', 'Motyw został zmieniony.');
    }

    /**
     * Export user data - handle different formats
     */
    public function exportData(Request $request)
    {
        $format = $request->get('format', 'json');

        switch($format) {
            case 'json':
                return $this->exportToJson();
            case 'csv':
                return $this->exportToCsv();
            case 'excel':
                return $this->exportToExcel();
            case 'sql':
                return $this->exportToSql();
            default:
                return $this->exportToJson();
        }
    }

    /**
     * Get user data for export
     */
    private function getUserData()
    {
        $user = Auth::user();

        $data = [
            'user_info' => [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'date_of_birth' => $user->date_of_birth?->format('Y-m-d'),
                'gender' => $user->gender,
                'emergency_contact' => $user->emergency_contact,
                'medical_history' => is_array($user->medical_history) ? implode('; ', $user->medical_history) : $user->medical_history,
                'preferences' => is_array($user->preferences) ? json_encode($user->preferences) : $user->preferences,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ],
        ];

        // Dodaj dokumenty medyczne dla pacjentów
        if ($user->role === 'user') {
            $documents = $user->patientDocuments()
                ->with('doctor:id,firstname,lastname')
                ->get();

            $data['medical_documents'] = $documents->map(function ($doc) {
                $doctorName = null;
                if ($doc->doctor) {
                    $doctorName = ($doc->doctor->firstname ?? '') . ' ' . ($doc->doctor->lastname ?? '');
                }

                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'type' => $doc->type,
                    'content' => $doc->content,
                    'notes' => $doc->notes,
                    'status' => $doc->status,
                    'document_date' => $doc->document_date?->format('Y-m-d'),
                    'doctor' => $doctorName,
                    'created_at' => $doc->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();

            $appointments = $user->patientAppointments()
                ->with('doctor:id,firstname,lastname')
                ->get();

            $data['appointments'] = $appointments->map(function ($app) {
                $doctorName = null;
                if ($app->doctor) {
                    $doctorName = ($app->doctor->firstname ?? '') . ' ' . ($app->doctor->lastname ?? '');
                }

                return [
                    'id' => $app->id,
                    'date' => $app->start_time ? \Carbon\Carbon::parse($app->start_time)->format('Y-m-d') : null,
                    'time' => $app->start_time ? \Carbon\Carbon::parse($app->start_time)->format('H:i') : null,
                    'type' => $app->type,
                    'status' => $app->status,
                    'notes' => $app->notes,
                    'doctor' => $doctorName,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();
        }

        // Dodaj dane dla doktorów
        if ($user->role === 'doctor') {
            $documents = $user->doctorDocuments()
                ->with('patient:id,firstname,lastname')
                ->get();

            $data['created_documents'] = $documents->map(function ($doc) {
                $patientName = null;
                if ($doc->patient) {
                    $patientName = ($doc->patient->firstname ?? '') . ' ' . ($doc->patient->lastname ?? '');
                }

                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'type' => $doc->type,
                    'content' => $doc->content,
                    'notes' => $doc->notes,
                    'status' => $doc->status,
                    'document_date' => $doc->document_date?->format('Y-m-d'),
                    'patient' => $patientName,
                    'created_at' => $doc->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();

            $appointments = $user->doctorAppointments()
                ->with('patient:id,firstname,lastname')
                ->get();

            $data['doctor_appointments'] = $appointments->map(function ($app) {
                $patientName = null;
                if ($app->patient) {
                    $patientName = ($app->patient->firstname ?? '') . ' ' . ($app->patient->lastname ?? '');
                }

                return [
                    'id' => $app->id,
                    'date' => $app->start_time ? \Carbon\Carbon::parse($app->start_time)->format('Y-m-d') : null,
                    'time' => $app->start_time ? \Carbon\Carbon::parse($app->start_time)->format('H:i') : null,
                    'type' => $app->type,
                    'status' => $app->status,
                    'notes' => $app->notes,
                    'patient' => $patientName,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();
        }

        $data['export_info'] = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => $user->firstname . ' ' . $user->lastname,
            'format' => 'user_data_export'
        ];

        return $data;
    }

    /**
     * Export to JSON
     */
    private function exportToJson()
    {
        $data = $this->getUserData();
        $user = Auth::user();

        $filename = 'user_data_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.json';

        return Response::make(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export to CSV
     */
    private function exportToCsv()
    {
        $data = $this->getUserData();
        $user = Auth::user();

        $filename = 'user_data_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.csv';

        $csv = "Pole,Wartosc\n";

        // User info
        foreach ($data['user_info'] as $key => $value) {
            $csv .= '"' . $key . '","' . str_replace('"', '""', $this->convertToString($value)) . '"' . "\n";
        }

        // Documents if exists
        if (isset($data['medical_documents'])) {
            $csv .= "\nDokumenty medyczne:\n";
            $csv .= "ID,Tytul,Typ,Status,Data dokumentu,Doktor,Data utworzenia\n";
            foreach ($data['medical_documents'] as $doc) {
                $csv .= implode(',', [
                    '"' . $doc['id'] . '"',
                    '"' . str_replace('"', '""', $doc['title']) . '"',
                    '"' . $doc['type'] . '"',
                    '"' . $doc['status'] . '"',
                    '"' . $doc['document_date'] . '"',
                    '"' . str_replace('"', '""', $doc['doctor'] ?? '') . '"',
                    '"' . $doc['created_at'] . '"'
                ]) . "\n";
            }
        }

        // Appointments if exists
        if (isset($data['appointments'])) {
            $csv .= "\nWizyty:\n";
            $csv .= "ID,Data,Godzina,Typ,Status,Doktor,Data utworzenia\n";
            foreach ($data['appointments'] as $app) {
                $csv .= implode(',', [
                    '"' . $app['id'] . '"',
                    '"' . $app['date'] . '"',
                    '"' . $app['time'] . '"',
                    '"' . $app['type'] . '"',
                    '"' . $app['status'] . '"',
                    '"' . str_replace('"', '""', $app['doctor'] ?? '') . '"',
                    '"' . $app['created_at'] . '"'
                ]) . "\n";
            }
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export to Excel (simple HTML table format)
     */
    private function exportToExcel()
    {
        $data = $this->getUserData();
        $user = Auth::user();

        $filename = 'user_data_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.xls';

        $excel = '<html><head><meta charset="UTF-8"></head><body>';
        $excel .= '<h2>Dane użytkownika - ' . htmlspecialchars($user->firstname . ' ' . $user->lastname) . '</h2>';

        // User info table
        $excel .= '<h3>Informacje podstawowe</h3>';
        $excel .= '<table border="1"><tr><th>Pole</th><th>Wartość</th></tr>';
        foreach ($data['user_info'] as $key => $value) {
            $excel .= '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($this->convertToString($value)) . '</td></tr>';
        }
        $excel .= '</table><br>';

        // Documents table
        if (isset($data['medical_documents']) && !empty($data['medical_documents'])) {
            $excel .= '<h3>Dokumenty medyczne</h3>';
            $excel .= '<table border="1">';
            $excel .= '<tr><th>ID</th><th>Tytuł</th><th>Typ</th><th>Status</th><th>Data dokumentu</th><th>Doktor</th><th>Data utworzenia</th></tr>';
            foreach ($data['medical_documents'] as $doc) {
                $excel .= '<tr>';
                $excel .= '<td>' . htmlspecialchars($doc['id']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($doc['title']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($doc['type']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($doc['status']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($doc['document_date']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($doc['doctor'] ?? '') . '</td>';
                $excel .= '<td>' . htmlspecialchars($doc['created_at']) . '</td>';
                $excel .= '</tr>';
            }
            $excel .= '</table><br>';
        }

        // Appointments table
        if (isset($data['appointments']) && !empty($data['appointments'])) {
            $excel .= '<h3>Wizyty</h3>';
            $excel .= '<table border="1">';
            $excel .= '<tr><th>ID</th><th>Data</th><th>Godzina</th><th>Typ</th><th>Status</th><th>Doktor</th><th>Data utworzenia</th></tr>';
            foreach ($data['appointments'] as $app) {
                $excel .= '<tr>';
                $excel .= '<td>' . htmlspecialchars($app['id']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($app['date']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($app['time']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($app['type']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($app['status']) . '</td>';
                $excel .= '<td>' . htmlspecialchars($app['doctor'] ?? '') . '</td>';
                $excel .= '<td>' . htmlspecialchars($app['created_at']) . '</td>';
                $excel .= '</tr>';
            }
            $excel .= '</table><br>';
        }

        $excel .= '<p>Eksport wygenerowany: ' . htmlspecialchars($data['export_info']['exported_at']) . '</p>';
        $excel .= '</body></html>';

        return Response::make($excel, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export to SQL
     */
    private function exportToSql()
    {
        $data = $this->getUserData();
        $user = Auth::user();

        $filename = 'user_data_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.sql';

        $sql = "-- Eksport danych użytkownika\n";
        $sql .= "-- Wygenerowany: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Użytkownik: " . $user->firstname . " " . $user->lastname . "\n\n";

        // User data
        $sql .= "-- Dane użytkownika\n";
        $sql .= "INSERT INTO users_export (field_name, field_value) VALUES\n";
        $values = [];
        foreach ($data['user_info'] as $key => $value) {
            $values[] = "('" . addslashes($key) . "', '" . addslashes($this->convertToString($value)) . "')";
        }
        $sql .= implode(",\n", $values) . ";\n\n";

        // Medical documents
        if (isset($data['medical_documents']) && !empty($data['medical_documents'])) {
            $sql .= "-- Dokumenty medyczne\n";
            $sql .= "INSERT INTO medical_documents_export (id, title, type, status, document_date, doctor, created_at) VALUES\n";
            $values = [];
            foreach ($data['medical_documents'] as $doc) {
                $values[] = "('" . addslashes($doc['id']) . "', '" . addslashes($doc['title']) . "', '" .
                           addslashes($doc['type']) . "', '" . addslashes($doc['status']) . "', '" .
                           addslashes($doc['document_date']) . "', '" . addslashes($doc['doctor'] ?? '') . "', '" .
                           addslashes($doc['created_at']) . "')";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }

        // Appointments
        if (isset($data['appointments']) && !empty($data['appointments'])) {
            $sql .= "-- Wizyty\n";
            $sql .= "INSERT INTO appointments_export (id, date, time, type, status, doctor, created_at) VALUES\n";
            $values = [];
            foreach ($data['appointments'] as $app) {
                $values[] = "('" . addslashes($app['id']) . "', '" . addslashes($app['date']) . "', '" .
                           addslashes($app['time']) . "', '" . addslashes($app['type']) . "', '" .
                           addslashes($app['status']) . "', '" . addslashes($app['doctor'] ?? '') . "', '" .
                           addslashes($app['created_at']) . "')";
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }

        return Response::make($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Convert any value to string safely
     */
    private function convertToString($value)
    {
        if (is_null($value)) {
            return '';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        // Walidacja hasła
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'confirmation' => 'required|in:DELETE',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Nieprawidłowe hasło.'])
                ->withInput();
        }

        // Usuń konto
        Auth::logout();
        $user->delete();

        return redirect()->route('login')
            ->with('success', 'Twoje konto zostało usunięte.');
    }
}

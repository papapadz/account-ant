<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HR\Company;
use App\Models\Person;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);

        $user = $request->user();
        $person = $user->person ?? Person::first() ?? Person::create([
            'first_name' => 'Alexander',
            'last_name' => 'Vance',
            'civil_status' => 'Single',
        ]);

        if (!$user->person_id) {
            $user->update(['person_id' => $person->id]);
        }

        $person->update($validated);

        return response()->json([
            'message' => 'Person profile updated successfully',
            'person' => $person,
        ]);
    }

    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string',
            'business_description' => 'required|string',
            'business_scope' => 'nullable|string',
            'city_id' => 'nullable|integer',
            'is_government' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $company = $user->personAffiliation?->company ?? Company::first() ?? Company::create([
            'business_name' => 'AccountAnt Tech Solutions Inc.',
            'business_description' => 'Automated Ledger System',
            'city_id' => 1,
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company settings updated successfully',
            'company' => $company,
        ]);
    }

    public function downloadBackup(Request $request)
    {
        $format = $request->query('format', 'sqlite');

        if ($format === 'json') {
            $data = [
                'exported_at' => now()->toIso8601String(),
                'system' => 'AccountAnt Ledger System',
                'version' => '1.0.0',
                'fund_accounts' => \App\Models\Accounting\FundAccount::all(),
                'ledger_accounts' => \App\Models\Accounting\LedgerAccount::all(),
                'account_items' => \App\Models\Accounting\AccountItem::all(),
                'journal_entries' => \App\Models\Accounting\LedgerAccountItem::all(),
                'projects' => \App\Models\Project::all(),
                'users' => \App\Models\User::all(),
                'company' => \App\Models\HR\Company::first(),
                'people' => \App\Models\Person::all(),
            ];

            $fileName = 'accountant-backup-' . date('Y-m-d_H-i-s') . '.json';
            return response()->streamDownload(function () use ($data) {
                echo json_encode($data, JSON_PRETTY_PRINT);
            }, $fileName, [
                'Content-Type' => 'application/json',
            ]);
        }

        // Default: raw SQLite database file download
        $possiblePaths = [
            config('database.connections.nativephp.database'),
            database_path('nativephp.sqlite'),
            config('database.connections.sqlite.database'),
            database_path('database.sqlite'),
        ];

        $dbPath = null;
        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path) && filesize($path) > 0) {
                $dbPath = $path;
                break;
            }
        }

        if ($dbPath) {
            $fileName = 'accountant-sqlite-backup-' . date('Y-m-d_H-i-s') . '.sqlite';
            return response()->download($dbPath, $fileName, [
                'Content-Type' => 'application/x-sqlite3',
            ]);
        }

        return response()->json([
            'message' => 'Database file not found or empty.',
        ], 404);
    }
}

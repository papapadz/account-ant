<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HR\Company;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Data tables cleared by wipeData(), ordered leaf-first so foreign keys never block a delete.
     * Users, roles, address, profile/company and framework tables are deliberately not listed.
     */
    private const WIPE_TABLES = [
        'journal_entry_items',
        'accounts_payables',
        'project_funds',
        'ledger_account_items',
        'account_items',
        'ledger_accounts',
        'projects',
        'fund_accounts',
    ];

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

    public function wipeData(Request $request)
    {
        if (!$request->user()->hasAnyRole(['super_admin', 'admin'])) {
            return response()->json(['message' => 'Only an admin or super admin can wipe data.'], 403);
        }

        $request->validate([
            'confirm' => 'required|in:WIPE',
        ]);

        $deleted = DB::transaction(function () {
            $counts = [];
            foreach (self::WIPE_TABLES as $table) {
                $counts[$table] = DB::table($table)->delete();
            }

            // Restart auto-increment IDs from 1 (SQLite keeps them in sqlite_sequence).
            if (DB::getDriverName() === 'sqlite') {
                DB::table('sqlite_sequence')->whereIn('name', self::WIPE_TABLES)->delete();
            }

            return $counts;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'All data wiped successfully',
            'data' => ['deleted' => $deleted],
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

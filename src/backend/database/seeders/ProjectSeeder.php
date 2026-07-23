<?php

namespace Database\Seeders;

use App\Models\Accounting\FundAccount;
use App\Models\Accounting\Project;
use App\Models\Accounting\ProjectFund;
use App\Models\Address\City;
use App\Models\HR\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $company = Company::first();
        $qcCity = City::where('name', 'Quezon City')->first() ?? City::first();
        $taguigCity = City::where('name', 'LIKE', '%Taguig%')->first() ?? City::first();

        $fund1 = FundAccount::where('fund_code', 'FND-101')->first();
        $fund2 = FundAccount::where('fund_code', 'FND-202')->first();

        // Project 1
        $project1 = Project::firstOrCreate(
            ['name' => 'National Smart Transit Public Ledger Initiative'],
            [
                'company_id' => $company ? $company->id : 1,
                'user_id' => $user ? $user->id : 1,
                'description' => 'Public infrastructure digital ledger tracking municipal transit fare collections and station maintenance allocations.',
                'budget' => 500000.00,
                'start_date' => '2026-07-01',
                'end_date' => '2027-06-30',
                'client_name' => 'Department of Transportation & Public Works',
                'is_government' => true,
                'city_id' => $qcCity ? $qcCity->id : null,
                'house_number' => '101',
                'street' => 'Elliptical Road',
                'village' => 'Diliman Complex',
                'barangay' => 'Barangay Central',
                'zip' => '1100',
            ]
        );

        // Project 2
        $project2 = Project::firstOrCreate(
            ['name' => 'Enterprise FinTech AI Ledger Engine'],
            [
                'company_id' => $company ? $company->id : 1,
                'user_id' => $user ? $user->id : 1,
                'description' => 'High-speed automated reconciliation engine and ledger posting pipeline for global banking partner.',
                'budget' => 250000.00,
                'start_date' => '2026-07-10',
                'end_date' => '2026-12-31',
                'client_name' => 'Apex Global Financial Corp',
                'is_government' => false,
                'city_id' => $taguigCity ? $taguigCity->id : null,
                'house_number' => 'Floor 18',
                'street' => '5th Avenue cor. 28th St.',
                'village' => 'Bonifacio Global City',
                'barangay' => 'Fort Bonifacio',
                'zip' => '1634',
            ]
        );

        // Fund Allocations
        if ($fund1) {
            ProjectFund::firstOrCreate(
                ['project_id' => $project1->id, 'fund_account_id' => $fund1->id],
                ['initial_amount' => 300000.00, 'user_id' => $user ? $user->id : 1]
            );

            ProjectFund::firstOrCreate(
                ['project_id' => $project2->id, 'fund_account_id' => $fund1->id],
                ['initial_amount' => 250000.00, 'user_id' => $user ? $user->id : 1]
            );
        }

        if ($fund2) {
            ProjectFund::firstOrCreate(
                ['project_id' => $project1->id, 'fund_account_id' => $fund2->id],
                ['initial_amount' => 200000.00, 'user_id' => $user ? $user->id : 1]
            );
        }
    }
}

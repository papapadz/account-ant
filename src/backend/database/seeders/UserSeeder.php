<?php

namespace Database\Seeders;

use App\Models\Address\City;
use App\Models\HR\Company;
use App\Models\HR\PersonAffiliation;
use App\Models\HR\Position;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::first();

        // 1. Ensure default company exists
        $company = Company::firstOrCreate(
            ['business_name' => 'AccountAnt Tech Solutions Inc.'],
            [
                'business_description' => 'Enterprise Financial Ledger & Cloud Automation Provider',
                'city_id' => $city ? $city->id : 1,
                'business_classification' => 'Financial Technology',
                'business_scope' => 'National',
                'street' => '5th Avenue',
                'building_number' => 'Floor 18',
                'barangay' => 'Fort Bonifacio',
                'zip' => '1634',
                'date_started' => '2026-01-01',
                'is_government' => false,
            ]
        );

        // 2. Define users to seed with HR personas
        $usersData = [
            [
                'email' => 'admin@accountant.io',
                'password' => 'password',
                'first_name' => 'Alexander',
                'last_name' => 'Vance',
                'middle_name' => 'Cross',
                'birth_date' => '1988-05-14',
                'gender' => 'Male',
                'civil_status' => 'Single',
                'position_title' => 'Finance Director',
                'employee_id' => 'EMP-2026-001',
                'affiliation_level' => 'Executive',
            ],
            [
                'email' => 'accountant@accountant.io',
                'password' => 'password',
                'first_name' => 'Sarah',
                'last_name' => 'Jenkins',
                'middle_name' => 'Elizabeth',
                'birth_date' => '1992-09-22',
                'gender' => 'Female',
                'civil_status' => 'Married',
                'position_title' => 'Senior Accountant',
                'employee_id' => 'EMP-2026-002',
                'affiliation_level' => 'Managerial',
            ],
            [
                'email' => 'auditor@accountant.io',
                'password' => 'password',
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'middle_name' => 'Wei',
                'birth_date' => '1990-11-05',
                'gender' => 'Male',
                'civil_status' => 'Single',
                'position_title' => 'Internal Auditor',
                'employee_id' => 'EMP-2026-003',
                'affiliation_level' => 'Supervisory',
            ],
        ];

        foreach ($usersData as $data) {
            // Create or retrieve Person record
            $person = Person::firstOrCreate(
                ['first_name' => $data['first_name'], 'last_name' => $data['last_name']],
                [
                    'middle_name' => $data['middle_name'],
                    'birth_date' => $data['birth_date'],
                    'gender' => $data['gender'],
                    'civil_status' => $data['civil_status'],
                ]
            );

            // Create or retrieve Position record
            $position = Position::firstOrCreate(
                ['title' => $data['position_title']],
                [
                    'industry' => 'Finance',
                    'salary_grade' => 20,
                ]
            );

            // Create or retrieve PersonAffiliation record
            $affiliation = PersonAffiliation::firstOrCreate(
                ['person_id' => $person->id, 'company_id' => $company->id],
                [
                    'affiliation_level' => $data['affiliation_level'],
                    'employment_status' => 'Regular',
                    'employee_id' => $data['employee_id'],
                    'position_id' => $position->id,
                    'is_head' => ($data['affiliation_level'] === 'Executive'),
                ]
            );

            // Create or update User record
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'password' => Hash::make($data['password']),
                    'person_id' => $person->id,
                    'person_affiliations_id' => $affiliation->id,
                ]
            );
        }
    }
}

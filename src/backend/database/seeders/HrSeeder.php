<?php

namespace Database\Seeders;

use App\Models\Address\City;
use App\Models\HR\Company;
use App\Models\HR\PersonAffiliation;
use App\Models\HR\Position;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;

class HrSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $city = City::first();

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
                'created_by' => $user ? $user->id : null,
            ]
        );

        $position = Position::firstOrCreate(
            ['title' => 'Finance Director'],
            [
                'industry' => 'Finance',
                'salary_grade' => 24,
            ]
        );

        $person = Person::firstOrCreate(
            ['first_name' => 'Alexander', 'last_name' => 'Vance'],
            [
                'middle_name' => 'Cross',
                'birth_date' => '1988-05-14',
                'gender' => 'Male',
                'civil_status' => 'Single',
            ]
        );

        PersonAffiliation::firstOrCreate(
            ['person_id' => $person->id, 'company_id' => $company->id],
            [
                'affiliation_level' => 'Executive',
                'employment_status' => 'Regular',
                'employee_id' => 'EMP-2026-001',
                'position_id' => $position->id,
                'is_head' => true,
            ]
        );

        if ($user && empty($user->person_id)) {
            $user->person_id = $person->id;
            $user->save();
        }
    }
}

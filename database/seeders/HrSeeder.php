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

class HrSeeder extends Seeder
{
    public function run(): void
    {
        
        /** 1. Setup Company */
        $city = City::first();
        $company = Company::firstOrCreate(
            [
                'business_name' => config('bee.company.business_name'),
            ],
            [
                'business_description' => config('bee.company.business_description'),
                'city_id' => $city ? $city->id : 1,
                'business_classification' => config('bee.company.business_classification'),
                'business_scope' => config('bee.company.business_scope'),
                'street' => config('bee.company.street'),
                'building_number' => config('bee.company.building_number'),
                'barangay' => config('bee.company.barangay'),
                'zip' => config('bee.company.zip'),
                'date_started' => config('bee.company.date_started'),
                'is_government' => config('bee.company.is_government')
            ]
        );

        /** 2. Setup Admin */
        $admin_position = Position::firstOrCreate(
            ['title' => 'System Administrator'],
            [
                'industry' => 'Information Technology'
            ]
        );
        $admin = Person::firstOrCreate(
            [
                'first_name' => config('bee.admin.first_name'), 
                'last_name' => config('bee.admin.last_name'),
                'birth_date' => config('bee.admin.birth_date'),
                'gender' => config('bee.admin.gender'),
                'civil_status' => config('bee.admin.civil_status'),
            ]
        );
        
        $admin_user = User::firstOrCreate(
            [
                'person_id' => $admin->id,
                'email' => config('bee.admin.email')
            ],
            [
                'password' => Hash::make(config('bee.admin.password')),
                'person_affiliations_id' => PersonAffiliation::firstOrCreate(
                    ['person_id' => $admin->id, 'company_id' => $company->id],
                    [
                        'affiliation_level' => 'Executive',
                        'employment_status' => 'Regular',
                        'employee_id' => 'bee-0001',
                        'position_id' => $admin_position->id,
                        'is_head' => false,
                    ]
                )->id
            ]
        );
        $admin_user->syncRoles('super_admin');

        /** 3. Setup User */
        $position = Position::firstOrCreate(
            ['title' => 'Owner'],
            [
                'industry' => 'Construction',
                'salary_grade' => 24,
            ]
        );
        $person = Person::firstOrCreate(
                    ['first_name' => config('bee.person.first_name'), 'last_name' => config('bee.person.last_name')],
                    [
                        'middle_name' => config('bee.person.middle_name'),
                        'birth_date' => config('bee.person.birth_date'),
                        'gender' => config('bee.person.gender'),
                        'civil_status' => config('bee.person.civil_status'),
                    ]
                );
        $user = User::firstOrCreate(
            [
                'person_id' => $person->id,
                'email' => config('bee.person.email')
            ],
            [
                'password' => Hash::make(config('bee.person.password')),
                'person_affiliations_id' => PersonAffiliation::firstOrCreate(
                    ['person_id' => $person->id, 'company_id' => $company->id],
                    [
                        'affiliation_level' => 'Executive',
                        'employment_status' => 'Regular',
                        'employee_id' => config('bee.person.employee_id'),
                        'position_id' => $position->id,
                        'is_head' => true,
                    ]
                )->id
            ]
        );
        $user->syncRoles('admin');
    }
}

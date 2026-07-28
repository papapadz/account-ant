<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HR\Company;
use App\Models\HR\PersonAffiliation;
use App\Models\HR\Position;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with(['person', 'personAffiliation.company', 'personAffiliation.position'])
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Dev fallback if user doesn't exist yet for admin@accountant.io
            if ($user === null && $request->email === 'admin@accountant.io') {
                $person = Person::create([
                    'first_name' => 'Alexander',
                    'last_name' => 'Vance',
                    'civil_status' => 'Single',
                    'gender' => 'Male',
                ]);

                $user = User::create([
                    'person_id' => $person->id,
                    'email' => 'admin@accountant.io',
                    'password' => Hash::make('password'),
                ]);

                $user->load(['person', 'personAffiliation.company', 'personAffiliation.position']);
            } else {
                return response()->json(['message' => 'Invalid email or password.'], 401);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|min:6',
            'person.first_name' => 'required|string',
            'person.last_name' => 'required|string',
            'company.business_name' => 'required|string',
            'company.business_description' => 'required|string',
            'position.title' => 'required|string',
            'affiliation.employee_id' => 'required|string',
        ]);

        $result = DB::transaction(function () use ($request) {
            // 1. Create Person
            $personData = $request->input('person');
            $person = Person::create([
                'first_name' => $personData['first_name'],
                'last_name' => $personData['last_name'],
                'middle_name' => $personData['middle_name'] ?? null,
                'civil_status' => $personData['civil_status'] ?? 'Single',
                'gender' => $personData['gender'] ?? 'Male',
                'birth_date' => $personData['birth_date'] ?? null,
            ]);

            // 2. Create Company
            $companyData = $request->input('company');
            $company = Company::create([
                'business_name' => $companyData['business_name'],
                'business_description' => $companyData['business_description'],
                'city_id' => $companyData['city_id'] ?? 1,
                'business_scope' => $companyData['business_scope'] ?? 'National',
                'is_government' => $companyData['is_government'] ?? false,
            ]);

            // 3. Create Position
            $positionData = $request->input('position');
            $position = Position::create([
                'title' => $positionData['title'],
                'industry' => $positionData['industry'] ?? 'Finance',
                'salary_grade' => $positionData['salary_grade'] ?? 18,
            ]);

            // 4. Create PersonAffiliation
            $affData = $request->input('affiliation');
            $affiliation = PersonAffiliation::create([
                'person_id' => $person->id,
                'company_id' => $company->id,
                'position_id' => $position->id,
                'affiliation_level' => $affData['affiliation_level'] ?? 'Executive',
                'employment_status' => $affData['employment_status'] ?? 'Regular',
                'employee_id' => $affData['employee_id'],
                'is_head' => $affData['is_head'] ?? true,
            ]);

            // 5. Create User
            $userData = $request->input('user');
            $user = User::create([
                'person_id' => $person->id,
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'person_affiliations_id' => $affiliation->id,
            ]);

            $user->load(['person', 'personAffiliation.company', 'personAffiliation.position']);

            return [
                'user' => $user,
                'person' => $person,
                'company' => $company,
                'position' => $position,
                'affiliation' => $affiliation,
            ];
        });

        $token = bin2hex(random_bytes(32));

        return response()->json([
            'message' => 'User and company onboarded successfully',
            'token' => $token,
            'data' => $result,
        ], 201);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->load(['person', 'personAffiliation.company', 'personAffiliation.position']);
        }
        return response()->json(['user' => $user]);
    }
}

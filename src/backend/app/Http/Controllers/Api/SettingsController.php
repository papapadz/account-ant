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

        $person = Person::first() ?? Person::create([
            'first_name' => 'Alexander',
            'last_name' => 'Sterling',
            'civil_status' => 'Single',
        ]);

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

        $company = Company::first() ?? Company::create([
            'business_name' => 'Apex Financial Technologies Inc.',
            'business_description' => 'Automated Ledger System',
            'city_id' => 1,
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company settings updated successfully',
            'company' => $company,
        ]);
    }
}

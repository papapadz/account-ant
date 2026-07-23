<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address\City;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    /**
     * Display a listing of active cities with state info.
     */
    public function index(): JsonResponse
    {
        $cities = City::with(['state', 'country'])
            ->where('flag', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $cities,
        ]);
    }
}

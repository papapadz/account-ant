<?php

use App\Http\Controllers\Api\AccountItemController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\FundAccountController;
use App\Http\Controllers\Api\LedgerAccountController;
use App\Http\Controllers\Api\LedgerAccountItemController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AccountAnt API Routes
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/', function () {
    return response()->json([
        'system' => 'AccountAnt Ledger System',
        'version' => '1.0.0',
        'status' => 'ONLINE',
    ]);
});

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Accounting Ledger Routes
    Route::apiResource('fund-accounts', FundAccountController::class);
    Route::apiResource('ledger-accounts', LedgerAccountController::class);
    Route::apiResource('account-items', AccountItemController::class);

    // Project Ledger Routes
    Route::post('/projects/{id}/funds', [ProjectController::class, 'addFund']);
    Route::apiResource('projects', ProjectController::class);
    Route::get('/cities', [CityController::class, 'index']);

    Route::get('/journal-entries/summary', [LedgerAccountItemController::class, 'summary']);
    Route::apiResource('journal-entries', LedgerAccountItemController::class);

    // Status Update Routes
    Route::patch('/ledger-accounts/{id}/status', [LedgerAccountController::class, 'updateStatus']);
    Route::patch('/account-items/{id}/status', [AccountItemController::class, 'updateStatus']);
    Route::patch('/journal-entries/{id}/status', [LedgerAccountItemController::class, 'updateStatus']);
    Route::patch('/journal-entries/{id}/is-paid', [LedgerAccountItemController::class, 'updatePaymentStatus']);
    Route::patch('/projects/{id}/status', [ProjectController::class, 'updateStatus']);

    // Settings Routes
    Route::prefix('settings')->group(function () {
        Route::put('/profile', [SettingsController::class, 'updateProfile']);
        Route::put('/company', [SettingsController::class, 'updateCompany']);
        Route::get('/backup', [SettingsController::class, 'downloadBackup']);
    });
});

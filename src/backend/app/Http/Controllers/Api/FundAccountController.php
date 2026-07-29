<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accounting\FundAccount;
use Illuminate\Http\Request;

class FundAccountController extends Controller
{
    public function index()
    {
        $funds = FundAccount::with(['company', 'user'])->get();

        // Seed fallback data if table is currently empty
        if ($funds->isEmpty()) {
            FundAccount::create([
                'company_id' => 1,
                'fund_code' => 'FND-101',
                'fund_name' => 'General Operating Fund',
                'description' => 'Primary corporate liquidity and daily operational expenses',
                'amount' => 500000.00,
                'user_id' => 1,
                'ledger_account_id' => 10,
            ]);
            FundAccount::create([
                'company_id' => 1,
                'fund_code' => 'FND-202',
                'fund_name' => 'Capital Expenditure & R&D Fund',
                'description' => 'Reserved capital for software automation & infrastructure expansion',
                'amount' => 750000.00,
                'user_id' => 1,
                'ledger_account_id' => 20,
            ]);
            $funds = FundAccount::with(['company', 'user'])->get();
        }

        return response()->json($funds);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fund_code' => 'required|string|max:20',
            'fund_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'amount' => 'numeric|min:0',
            'company_id' => 'integer',
        ]);

        $fund = FundAccount::create([
            'company_id' => $validated['company_id'] ?? 1,
            'fund_code' => $validated['fund_code'],
            'fund_name' => $validated['fund_name'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'] ?? 0.00,
            'user_id' => 1,
            'ledger_account_id' => 1,
        ]);

        return response()->json([
            'message' => 'Fund account created successfully',
            'data' => $fund,
        ], 201);
    }

    public function show($id)
    {
        $fund = FundAccount::with('ledgerAccounts')->findOrFail($id);
        return response()->json($fund);
    }

    public function destroy($id)
    {
        $fund = FundAccount::findOrFail($id);
        $fund->delete();
        return response()->json(['message' => 'Fund account deleted successfully']);
    }
}

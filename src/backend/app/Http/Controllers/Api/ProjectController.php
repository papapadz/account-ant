<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Project;
use App\Models\Accounting\ProjectFund;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects with address & budget metrics.
     */
    public function index(): JsonResponse
    {
        $projects = Project::with(['city', 'projectFunds.fundAccount', 'journalEntries'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $projects,
        ]);
    }

    /**
     * Store a newly created project in storage (Step 1).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'client_name' => 'required|string|max:150',
            'is_government' => 'boolean',
            'city_id' => 'nullable|exists:cities,id',
            'house_number' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'barangay' => 'required|string|max:100',
            'zip' => 'required|string|max:10',
        ]);

        $validated['user_id'] = $request->user() ? $request->user()->id : 1;

        $project = Project::create($validated);
        $project->load(['city', 'projectFunds.fundAccount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Project created successfully',
            'data' => $project,
        ], 201);
    }

    /**
     * Display the specified project with fund sources & balance metrics.
     */
    public function show(string $id): JsonResponse
    {
        $project = Project::with([
            'city',
            'projectFunds.fundAccount',
            'journalEntries.ledgerAccount',
            'journalEntries.accountItem',
            'journalEntries.fundAccount'
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $project,
        ]);
    }

    /**
     * Attach a Fund Source to the Project with an Initial Amount (Steps 2 & 3).
     */
    public function addFund(Request $request, string $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'fund_account_id' => 'required|exists:fund_accounts,id',
            'initial_amount' => 'required|numeric|min:0',
        ]);

        // Prevent duplicate fund allocation or update existing
        $projectFund = ProjectFund::updateOrCreate(
            [
                'project_id' => $project->id,
                'fund_account_id' => $validated['fund_account_id'],
            ],
            [
                'initial_amount' => $validated['initial_amount'],
                'user_id' => $request->user() ? $request->user()->id : 1,
            ]
        );

        $project->load(['projectFunds.fundAccount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Fund source allocated to project successfully',
            'data' => $projectFund,
            'project' => $project,
        ]);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(string $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Project deleted successfully',
        ]);
    }
}

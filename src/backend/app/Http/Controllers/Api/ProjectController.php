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
        $projects = Project::with(['city', 'projectFunds.fundAccount', 'journalEntries.items'])
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
            'journalEntries.fundAccount',
            'journalEntries.items'
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $project,
        ]);
    }

    /**
     * Update the specified project's details.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'budget' => 'sometimes|required|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'client_name' => 'sometimes|required|string|max:150',
            'status' => 'sometimes|in:active,on-hold,completed,cancelled',
            'is_government' => 'sometimes|boolean',
            'city_id' => 'nullable|exists:cities,id',
            'house_number' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'barangay' => 'sometimes|required|string|max:100',
            'zip' => 'sometimes|required|string|max:10',
        ]);

        $project->update($validated);
        $project->load(['city', 'projectFunds.fundAccount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully',
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
            'initial_amount' => 'nullable|numeric|min:0',
            'date_received' => 'nullable|date',
            'date' => 'nullable|date',
        ]);

        $dateReceived = $validated['date_received'] ?? $validated['date'] ?? now()->toDateString();
        $initialAmount = isset($validated['initial_amount']) ? (float) $validated['initial_amount'] : 0.00;

        // Prevent duplicate fund allocation or update existing
        $projectFund = ProjectFund::updateOrCreate(
            [
                'project_id' => $project->id,
                'fund_account_id' => $validated['fund_account_id'],
            ],
            [
                'initial_amount' => $initialAmount,
                'date_received' => $dateReceived,
                'user_id' => $request->user() ? $request->user()->id : 1,
            ]
        );

        $project->load(['projectFunds.fundAccount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Fund source linked to project successfully',
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

    /**
     * Update the status of a project.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,on-hold,completed,cancelled',
        ]);

        $project = Project::findOrFail($id);
        $project->update(['status' => $validated['status']]);
        $project->load(['city', 'projectFunds.fundAccount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Project status updated successfully',
            'data' => $project,
        ]);
    }
}

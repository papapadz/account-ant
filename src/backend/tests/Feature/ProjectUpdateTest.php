<?php

namespace Tests\Feature;

use App\Models\Accounting\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(User $user): Project
    {
        return Project::create([
            'user_id' => $user->id,
            'name' => 'City Bridge Rehabilitation',
            'client_name' => 'Department of Public Works',
            'budget' => 1000,
            'start_date' => '2026-01-15',
            'barangay' => 'Central',
            'zip' => '1000',
        ]);
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_update_project_details_and_status()
    {
        $user = $this->makeUser();
        $project = $this->makeProject($user);

        $response = $this->actingAs($user)->putJson("/api/projects/{$project->id}", [
            'name' => 'Renamed Bridge',
            'client_name' => 'DPWH',
            'budget' => 2500,
            'start_date' => '2026-02-01',
            'status' => 'on-hold',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Renamed Bridge')
            ->assertJsonPath('data.status', 'on-hold');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Renamed Bridge',
            'client_name' => 'DPWH',
            'budget' => 2500,
            'status' => 'on-hold',
        ]);
    }

    public function test_can_cancel_project_via_update()
    {
        $user = $this->makeUser();
        $project = $this->makeProject($user);

        $this->actingAs($user)->putJson("/api/projects/{$project->id}", [
            'status' => 'cancelled',
        ])->assertStatus(200)->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'cancelled']);
    }

    public function test_can_cancel_project_via_status_endpoint()
    {
        $user = $this->makeUser();
        $project = $this->makeProject($user);

        $this->actingAs($user)->patchJson("/api/projects/{$project->id}/status", [
            'status' => 'cancelled',
        ])->assertStatus(200);

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'status' => 'cancelled']);
    }

    public function test_update_rejects_invalid_payload()
    {
        $user = $this->makeUser();
        $project = $this->makeProject($user);

        $this->actingAs($user)->putJson("/api/projects/{$project->id}", [
            'budget' => -5,
            'status' => 'bogus',
        ])->assertStatus(422)->assertJsonValidationErrors(['budget', 'status']);
    }
}

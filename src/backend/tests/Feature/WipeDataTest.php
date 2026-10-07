<?php

namespace Tests\Feature;

use App\Models\Accounting\FundAccount;
use App\Models\Accounting\Project;
use App\Models\HR\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WipeDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fixtures reference a company/city chain these tests don't care about.
        // Deferred checks run at commit, which never happens because RefreshDatabase rolls back.
        DB::statement('PRAGMA defer_foreign_keys = ON');
    }

    private function makeUser(?string $role = null): User
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        if ($role) {
            Role::findOrCreate($role, 'web');
            $user->assignRole($role);
        }

        return $user;
    }

    private function seedData(User $user): void
    {
        FundAccount::create([
            'company_id' => 1,
            'fund_code' => 'FND-101',
            'fund_name' => 'General Operating Fund',
            'amount' => 1000,
            'user_id' => $user->id,
            'ledger_account_id' => 1,
        ]);

        Project::create([
            'user_id' => $user->id,
            'name' => 'City Bridge Rehabilitation',
            'client_name' => 'Department of Public Works',
            'budget' => 1000,
            'start_date' => '2026-01-15',
            'barangay' => 'Central',
            'zip' => '1000',
        ]);

        Company::create([
            'business_name' => 'MRR Construction',
            'business_description' => 'Builders',
            'city_id' => 1,
        ]);
    }

    public function test_admin_can_wipe_data()
    {
        $user = $this->makeUser('admin');
        $this->seedData($user);

        $this->actingAs($user)->deleteJson('/api/settings/wipe', ['confirm' => 'WIPE'])
            ->assertStatus(200);

        $this->assertDatabaseCount('fund_accounts', 0);
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_super_admin_can_wipe_data_but_keeps_users_and_companies()
    {
        $user = $this->makeUser('super_admin');
        $this->seedData($user);

        $this->actingAs($user)->deleteJson('/api/settings/wipe', ['confirm' => 'WIPE'])
            ->assertStatus(200)
            ->assertJsonPath('data.deleted.fund_accounts', 1)
            ->assertJsonPath('data.deleted.projects', 1);

        $this->assertDatabaseCount('fund_accounts', 0);
        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('roles', 1);
    }

    public function test_user_without_admin_role_is_forbidden()
    {
        $user = $this->makeUser();
        $this->seedData($user);

        $this->actingAs($user)->deleteJson('/api/settings/wipe', ['confirm' => 'WIPE'])
            ->assertStatus(403);

        $this->assertDatabaseCount('fund_accounts', 1);
        $this->assertDatabaseCount('projects', 1);
    }

    public function test_wipe_requires_exact_confirmation_word()
    {
        $user = $this->makeUser('admin');
        $this->seedData($user);

        $this->actingAs($user)->deleteJson('/api/settings/wipe', ['confirm' => 'wipe'])
            ->assertStatus(422)->assertJsonValidationErrors(['confirm']);
        $this->actingAs($user)->deleteJson('/api/settings/wipe')
            ->assertStatus(422)->assertJsonValidationErrors(['confirm']);

        $this->assertDatabaseCount('fund_accounts', 1);
        $this->assertDatabaseCount('projects', 1);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Accounting\FundAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FundAccountUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fund fixtures reference a company (and its city/state chain) that these tests don't care about.
        // Deferred checks are evaluated at commit, which never happens because RefreshDatabase rolls back.
        DB::statement('PRAGMA defer_foreign_keys = ON');
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    private function makeFund(User $user): FundAccount
    {
        return FundAccount::create([
            'company_id' => 1,
            'fund_code' => 'FND-101',
            'fund_name' => 'General Operating Fund',
            'description' => 'Daily operations',
            'amount' => 1000,
            'user_id' => $user->id,
            'ledger_account_id' => 1,
        ]);
    }

    public function test_new_fund_defaults_to_active()
    {
        $fund = $this->makeFund($this->makeUser());

        $this->assertSame('active', $fund->fresh()->status);
    }

    public function test_can_set_fund_inactive()
    {
        $user = $this->makeUser();
        $fund = $this->makeFund($user);

        $this->actingAs($user)->putJson("/api/fund-accounts/{$fund->id}", [
            'status' => 'inactive',
        ])->assertStatus(200)->assertJsonPath('data.status', 'inactive');

        $this->assertDatabaseHas('fund_accounts', ['id' => $fund->id, 'status' => 'inactive']);
    }

    public function test_can_update_other_fields_without_touching_status()
    {
        $user = $this->makeUser();
        $fund = $this->makeFund($user);

        $this->actingAs($user)->putJson("/api/fund-accounts/{$fund->id}", [
            'fund_name' => 'Renamed Fund',
            'amount' => 2500,
        ])->assertStatus(200);

        $this->assertDatabaseHas('fund_accounts', [
            'id' => $fund->id,
            'fund_name' => 'Renamed Fund',
            'amount' => 2500,
            'status' => 'active',
        ]);
    }

    public function test_update_rejects_invalid_status()
    {
        $user = $this->makeUser();
        $fund = $this->makeFund($user);

        $this->actingAs($user)->putJson("/api/fund-accounts/{$fund->id}", [
            'status' => 'bogus',
        ])->assertStatus(422)->assertJsonValidationErrors(['status']);
    }
}

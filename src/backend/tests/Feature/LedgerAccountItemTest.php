<?php

namespace Tests\Feature;

use App\Models\Accounting\AccountItem;
use App\Models\Accounting\FundAccount;
use App\Models\Accounting\LedgerAccount;
use App\Models\Accounting\LedgerAccountItem;
use App\Models\Accounting\Project;
use App\Models\Accounting\ProjectFund;
use App\Models\HR\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerAccountItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_post_journal_entry_with_itemized_line_items()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $region = new \App\Models\Address\Region();
        $region->name = 'Asia';
        $region->save();

        $subRegion = new \App\Models\Address\SubRegion();
        $subRegion->name = 'South-Eastern Asia';
        $subRegion->region_id = $region->id;
        $subRegion->save();

        $country = new \App\Models\Address\Country();
        $country->name = 'Philippines';
        $country->iso2 = 'PH';
        $country->iso3 = 'PHL';
        $country->subregion_id = $subRegion->id;
        $country->save();

        $state = new \App\Models\Address\State();
        $state->name = 'Metro Manila';
        $state->country_id = $country->id;
        $state->country_code = 'PH';
        $state->save();

        $city = new \App\Models\Address\City();
        $city->name = 'Manila';
        $city->state_id = $state->id;
        $city->state_code = 'MM';
        $city->country_id = $country->id;
        $city->country_code = 'PH';
        $city->latitude = 14.5995;
        $city->longitude = 120.9842;
        $city->flag = true;
        $city->wikiDataId = 'Q1461';
        $city->save();

        $company = Company::create([
            'business_name' => 'Acme Construction Corp',
            'business_description' => 'General Construction Contracting',
            'business_classification' => 'Corporation',
            'business_scope' => 'National',
            'city_id' => $city->id,
            'barangay' => 'Central',
            'zip' => '1000',
            'created_by' => $user->id,
        ]);

        $fundAccount = new FundAccount();
        $fundAccount->company_id = $company->id;
        $fundAccount->user_id = $user->id;
        $fundAccount->fund_code = 'FND-101';
        $fundAccount->fund_name = 'Infrastructure CapEx Fund';
        $fundAccount->amount = 500000.00;
        $fundAccount->ledger_account_id = 1;
        $fundAccount->save();

        $ledgerAccount = new LedgerAccount();
        $ledgerAccount->user_id = $user->id;
        $ledgerAccount->account_code = '5010';
        $ledgerAccount->account_name = 'Construction Materials';
        $ledgerAccount->fund_account_id = $fundAccount->id;
        $ledgerAccount->ledger_account_id = 1;
        $ledgerAccount->save();

        $project = Project::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'city_id' => $city->id,
            'name' => 'City Bridge Rehabilitation',
            'client_name' => 'Department of Public Works',
            'start_date' => '2026-01-15',
            'status' => 'active',
            'barangay' => 'Central',
            'zip' => '1000',
        ]);

        ProjectFund::create([
            'project_id' => $project->id,
            'fund_account_id' => $fundAccount->id,
            'initial_amount' => 250000.00,
        ]);

        $accountItem = AccountItem::create([
            'item_code' => 'MAT-CEM',
            'item_name' => 'Structural Cement & Rebar',
            'status' => 'active',
            'ledger_account_id' => $ledgerAccount->id,
            'transaction_type' => 'debit',
        ]);

        $payload = [
            'project_id' => $project->id,
            'fund_account_id' => $fundAccount->id,
            'ledger_account_id' => $ledgerAccount->id,
            'account_item_id' => $accountItem->id,
            'amount' => 2150.00,
            'transaction_type' => 'debit',
            'description' => 'Invoice #PO-8812 Cement and Rebar Delivery',
            'items' => [
                [
                    'description' => 'Portland Cement Type 1',
                    'quantity' => 100,
                    'unit' => 'bags',
                    'price' => 12.50,
                ],
                [
                    'description' => '12mm Deformed Steel Bar',
                    'quantity' => 50,
                    'unit' => 'pcs',
                    'price' => 18.00,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/journal-entries', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', '2150.00')
            ->assertJsonCount(2, 'data.items');

        $this->assertDatabaseHas('ledger_account_items', [
            'project_id' => $project->id,
            'amount' => 2150.00,
        ]);

        $this->assertDatabaseHas('journal_entry_items', [
            'description' => 'Portland Cement Type 1',
            'quantity' => 100.00,
            'unit' => 'bags',
            'price' => 12.50,
            'subtotal' => 1250.00,
        ]);

        $this->assertDatabaseHas('journal_entry_items', [
            'description' => '12mm Deformed Steel Bar',
            'quantity' => 50.00,
            'unit' => 'pcs',
            'price' => 18.00,
            'subtotal' => 900.00,
        ]);

        $indexResponse = $this->actingAs($user)->getJson("/api/journal-entries?project_id={$project->id}");
        $indexResponse->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonCount(2, '0.items');
    }
}

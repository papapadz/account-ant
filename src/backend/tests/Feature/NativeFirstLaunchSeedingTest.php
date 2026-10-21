<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\NativeAppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NativeFirstLaunchSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_launch_seeds_roles_address_data_and_the_admin_accounts(): void
    {
        $this->assertSame(0, Role::count());

        app(NativeAppServiceProvider::class)->runSeeders();

        $this->assertGreaterThan(0, Role::count());
        $this->assertGreaterThan(0, DB::table('countries')->count());
        // Check values, not only row counts: a seeder that writes empty rows would still pass a count.
        $this->assertSame(0, DB::table('regions')->whereNull('name')->count());
        $this->assertGreaterThan(0, DB::table('regions')->count());
        // HrSeeder creates two accounts: the super admin and the admin-role account.
        $this->assertSame(2, User::count());
        $this->assertTrue(User::where('email', config('bee.admin.email'))->firstOrFail()->hasRole('super_admin'));
    }

    public function test_second_call_changes_nothing(): void
    {
        $provider = app(NativeAppServiceProvider::class);
        $provider->runSeeders();

        $roles = Role::count();
        $countries = DB::table('countries')->count();

        $provider->runSeeders();

        $this->assertSame($roles, Role::count());
        $this->assertSame($countries, DB::table('countries')->count());
    }

    public function test_does_nothing_when_roles_already_exist(): void
    {
        Role::create(['name' => 'existing', 'guard_name' => 'web']);

        app(NativeAppServiceProvider::class)->runSeeders();

        $this->assertSame(1, Role::count());
        $this->assertSame(0, DB::table('countries')->count());
    }
}

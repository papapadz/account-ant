<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Database\Seeders\HrSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\WorldAddressSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    // Seeded on first launch, in dependency order. HrSeeder creates the company and two
    // accounts whose passwords are hardcoded in config/bee.php: change them before release.
    // DeviceInfoSeeder is left out: its record comes from config/bee.php and nothing at runtime reads it.
    private const FIRST_LAUNCH_SEEDERS = [
        RolesSeeder::class,
        WorldAddressSeeder::class,
        HrSeeder::class,
    ];

    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $this->runSeeders();

        Window::open()
            ->title(config('app.name'))
            ->hideMenu()
            ->maximized();
    }


    /**
     * Seed reference data on the first launch, when the roles table is empty.
     * The transaction keeps the seeders all-or-nothing, so a failed launch retries next time.
     */
    public function runSeeders(): void
    {
        if (! Schema::hasTable('roles') || DB::table('roles')->exists()) {
            return;
        }

        try {
            DB::transaction(function () {
                // Same wrapper as db:seed (SeedCommand). Address models have no $fillable,
                // so mass assignment throws unless guarding is off.
                Model::unguarded(function () {
                    foreach (self::FIRST_LAUNCH_SEEDERS as $seeder) {
                        app($seeder)->run();
                    }
                });
            });
        } catch (\Throwable $e) {
            Log::warning('First-launch seeding failed: '.$e->getMessage());
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '0',
            'max_input_time' => '0',
        ];
    }
    
}

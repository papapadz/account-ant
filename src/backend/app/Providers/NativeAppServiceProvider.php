<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->title(config('app.name'))
            ->hideMenu()
            ->maximized();
    }


    public function runSeeders(): void
    {
        if (Schema::hasTable('migrations')) {
            try {
                // Force the seeder to run seamlessly in production
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('Migration and seeding checks failed: ' . $e->getMessage());
            }
        } else {
            try {
                Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('First-time seeding encountered issue: ' . $e->getMessage());
            }
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

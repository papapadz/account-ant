<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\AutoUpdater;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\HR\Company;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        AutoUpdater::checkForUpdates();

        Window::open()
            ->title(config('app.name'))
            ->hideMenu()
            ->maximized();
    }

    /**
     * Check if database migrations have been executed.
     */
    public function isDatabaseMigrated(): bool
    {
        try {
            return Schema::hasTable('migrations') && Schema::hasTable('users');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check if the database has been seeded with initial seed data.
     */
    public function isDatabaseSeeded(): bool
    {
        try {
            if (!$this->isDatabaseMigrated()) {
                return false;
            }

            return User::exists() && Company::exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        try {
            if (!$this->isDatabaseMigrated()) {
                Artisan::call('migrate', ['--force' => true]);
            }

            if (!$this->isDatabaseSeeded()) {
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Log or swallow if database is locked or initializing
        }
        
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '0',
            'max_input_time' => '0',
        ];
    }
}

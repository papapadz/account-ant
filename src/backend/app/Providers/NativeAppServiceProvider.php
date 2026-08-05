<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->title('Account-Ant - Accounting System')
            ->hideMenu()
            ->maximized();

        // Artisan::call('migrate', ['--force' => true]);
        // $output = Artisan::output();

        // if (!str_contains($output, 'Nothing to migrate')) {
        //     Artisan::call('db:seed', [
        //         '--class' => 'Database\\Seeders\\DatabaseSeeder',
        //         '--force' => true,
        //     ]);
        // }
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

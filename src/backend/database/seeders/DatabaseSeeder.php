<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@accountant.io'],
            [
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            AddressSeeder::class,
            HrSeeder::class,
            AccountingSeeder::class,
            ProjectSeeder::class,
            LedgerItemSeeder::class,
        ]);
    }
}

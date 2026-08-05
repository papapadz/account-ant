<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeviceInfo;

class DeviceInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeviceInfo::firstOrCreate([
                'serial_number' => config('bee.device_info.serial_number')
            ],
            [
                'device_name' => config('bee.device_info.name'),
                'device_location' => config('bee.device_info.location'),
                'company_id' => 1
            ]);
    }
}

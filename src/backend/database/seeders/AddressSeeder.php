<?php

namespace Database\Seeders;

use App\Models\Address\City;
use App\Models\Address\Country;
use App\Models\Address\Region;
use App\Models\Address\State;
use App\Models\Address\SubRegion;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::firstOrCreate(
            ['name' => 'Asia'],
            ['translations' => 'Asia Region', 'flag' => true]
        );

        $subRegion = SubRegion::firstOrCreate(
            ['name' => 'South-Eastern Asia'],
            ['region_id' => $region->id, 'flag' => true]
        );

        $country = Country::firstOrCreate(
            ['name' => 'Philippines'],
            [
                'iso2' => 'PH',
                'iso3' => 'PHL',
                'numeric_code' => '608',
                'phonecode' => '63',
                'capital' => 'Manila',
                'currency' => 'PHP',
                'currency_name' => 'Philippine Peso',
                'currency_symbol' => '₱',
                'region' => 'Asia',
                'region_id' => $region->id,
                'subregion' => 'South-Eastern Asia',
                'subregion_id' => $subRegion->id,
                'nationality' => 'Filipino',
                'latitude' => 13.00000000,
                'longitude' => 122.00000000,
            ]
        );

        $state = State::firstOrCreate(
            ['name' => 'Metro Manila'],
            [
                'country_id' => $country->id,
                'country_code' => 'PH',
                'iso2' => 'MM',
                'type' => 'Metropolitan Area',
                'latitude' => 14.59950000,
                'longitude' => 120.98420000,
            ]
        );

        $cities = [
            [
                'name' => 'Quezon City',
                'state_id' => $state->id,
                'state_code' => 'MM',
                'country_id' => $country->id,
                'country_code' => 'PH',
                'latitude' => 14.67600000,
                'longitude' => 121.04370000,
            ],
            [
                'name' => 'Taguig City (BGC)',
                'state_id' => $state->id,
                'state_code' => 'MM',
                'country_id' => $country->id,
                'country_code' => 'PH',
                'latitude' => 14.51760000,
                'longitude' => 121.05090000,
            ],
            [
                'name' => 'Pasig City (Ortigas)',
                'state_id' => $state->id,
                'state_code' => 'MM',
                'country_id' => $country->id,
                'country_code' => 'PH',
                'latitude' => 14.57640000,
                'longitude' => 121.08510000,
            ],
            [
                'name' => 'Cebu City',
                'state_id' => $state->id,
                'state_code' => 'MM',
                'country_id' => $country->id,
                'country_code' => 'PH',
                'latitude' => 10.31570000,
                'longitude' => 123.88540000,
            ],
            [
                'name' => 'Davao City',
                'state_id' => $state->id,
                'state_code' => 'MM',
                'country_id' => $country->id,
                'country_code' => 'PH',
                'latitude' => 7.19070000,
                'longitude' => 125.45530000,
            ],
        ];

        foreach ($cities as $cityData) {
            City::firstOrCreate(['name' => $cityData['name']], $cityData);
        }
    }
}

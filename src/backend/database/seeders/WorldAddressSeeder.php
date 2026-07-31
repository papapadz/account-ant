<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Eloquent;
use DB;
use File;
use App\Models\Address\Country;
use App\Models\Address\Region;
use App\Models\Address\SubRegion;
use App\Models\Address\State;
use App\Models\Address\City;

class WorldAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Eloquent::unguard();
        //DB::unprepared(file_get_contents('database/raw/world_except_ph.sql'));
        //$this->command->info('World Address seeded!');
        //Eloquent::reguard();

        $jsonCountries = File::get('database/json/countries.json');
        $jsonWorldRegions = File::get('database/json/regions.json');
        $jsonWorldSubRegions = File::get('database/json/subregions.json');
        
        $jsonRegions = File::get('database/json/refregion.json');
        $jsonProvinces = File::get('database/json/refprovince.json');
        $jsonCities = File::get('database/json/refcitymun.json');

        foreach (json_decode($jsonWorldRegions) as $item)
            Region::firstOrCreate([
                'id' => $item->id
            ],[
                'name' => $item->name,
                'translations' => $item->translations,
                'created_at' => now(),
                'updated_at' => now(),
                'flag' => $item->flag,
                'wikiDataId' => $item->wikiDataId,
            ]);

        foreach (json_decode($jsonWorldSubRegions) as $item)
            SubRegion::firstOrCreate([
                'id' => $item->id
            ],[
                'name' => $item->name,
                'translations' => $item->translations,
                'region_id' => $item->region_id,
                'created_at' => now(),
                'updated_at' => now(),
                'flag' => $item->flag,
                'wikiDataId' => $item->wikiDataId,
            ]);
        
        foreach (json_decode($jsonCountries) as $item)
            Country::firstOrCreate([
                'id' => $item->id
            ],[
                'name' => $item->name,
                'iso3' => $item->iso3,
                'numeric_code' => $item->numeric_code,
                'iso2' => $item->iso2,
                'phonecode' => $item->phonecode,
                'capital' => $item->capital,
                'currency' => $item->currency,
                'currency_name' => $item->currency_name,
                'currency_symbol' => $item->currency_symbol,
                'tld' => $item->tld,
                'native' => $item->native,
                'region' => $item->region,
                'region_id' => $item->region_id,
                'subregion' => $item->subregion,
                'subregion_id' => $item->subregion_id,
                'nationality' => $item->nationality,
                'timezones' => $item->timezones,    
                'translations' => $item->translations,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'emoji' => $item->emoji,
                'emojiU' => $item->emojiU,
                'created_at' => now(),
                'updated_at' => now(),
                'flag' => $item->flag,
                'wikiDataId' => $item->wikiDataId
            ]);

        foreach (json_decode($jsonRegions) as $item) {
        	State::firstOrCreate([
                'country_id' => 174,
                'iso2' => $item->psgcCode,
                'name' => $item->regDesc,
                'country_code' => 'PH',
                'fips_code' => $item->regCode,
                'type' => 'region',
            ],[
                'flag' => 1
            ]);
        }

        foreach (json_decode($jsonProvinces) as $item) {
        	State::firstOrCreate([
                'country_id' => 174,
                'iso2' => $item->psgcCode,
                'name' => $item->provDesc,
                'country_code' => $item->regCode,
                'type' => 'province',
                'fips_code' => $item->provCode,
            ],[
                'flag' => 1,
            ]);
        }

        foreach (json_decode($jsonCities) as $item) {

            $state = State::where('fips_code',$item->provCode)->first();

        	City::firstOrCreate([
                'country_id' => 174,
                'state_id' => $state->id,
                'name' => $item->citymunDesc,
                'state_code' => $item->citymunCode,
                'country_code' => $item->regDesc,
            ],[
                'flag' => 1,
                'wikiDataId' => $item->psgcCode,
                'latitude' => 0,
                'longitude' => 0
            ]);
        }

        $this->command->info('Philippines Addresses seeded!');
    }
}

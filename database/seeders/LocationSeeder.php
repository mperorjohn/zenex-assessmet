<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create USA
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'United States',
            'iso3' => 'USA',
            'numeric_code' => '840',
            'iso2' => 'US',
            'phonecode' => '+1',
            'capital' => 'Washington',
            'currency' => 'USD',
            'currency_name' => 'US Dollar',
            'currency_symbol' => '$',
            'tld' => '.us',
            'native' => 'United States',
            'region' => 'Americas',
            'subregion' => 'Northern America',
            'latitude' => 38.00000000,
            'longitude' => -97.00000000,
            'emoji' => '🇺🇸',
            'emojiU' => 'U+1F1FA U+1F1F8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create California
        $stateId = DB::table('states')->insertGetId([
            'name' => 'California',
            'country_id' => $countryId,
            'country_code' => 'US',
            'fips_code' => '06',
            'iso2' => 'CA',
            'type' => 'state',
            'latitude' => 36.77826100,
            'longitude' => -119.41793240,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Los Angeles
        DB::table('cities')->insert([
            'name' => 'Los Angeles',
            'state_id' => $stateId,
            'state_code' => 'CA',
            'country_id' => $countryId,
            'country_code' => 'US',
            'latitude' => 34.05223390,
            'longitude' => -118.24368490,
            'created_at' => '2014-01-01 05:31:01',
            'updated_at' => now(),
        ]);

        // Create Nigeria
        $nigeriaId = DB::table('countries')->insertGetId([
            'name' => 'Nigeria',
            'iso3' => 'NGA',
            'numeric_code' => '566',
            'iso2' => 'NG',
            'phonecode' => '+234',
            'capital' => 'Abuja',
            'currency' => 'NGN',
            'currency_name' => 'Nigerian Naira',
            'currency_symbol' => '₦',
            'tld' => '.ng',
            'native' => 'Nigeria',
            'region' => 'Africa',
            'subregion' => 'Western Africa',
            'latitude' => 9.08200000,
            'longitude' => 8.67530000,
            'emoji' => '🇳🇬',
            'emojiU' => 'U+1F1F3 U+1F1EC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Lagos State
        $lagosStateId = DB::table('states')->insertGetId([
            'name' => 'Lagos',
            'country_id' => $nigeriaId,
            'country_code' => 'NG',
            'iso2' => 'LA',
            'type' => 'state',
            'latitude' => 6.52438100,
            'longitude' => 3.37920900,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Lagos City
        DB::table('cities')->insert([
            'name' => 'Lagos',
            'state_id' => $lagosStateId,
            'state_code' => 'LA',
            'country_id' => $nigeriaId,
            'country_code' => 'NG',
            'latitude' => 6.52437900,
            'longitude' => 3.37920900,
            'created_at' => '2014-01-01 05:31:01',
            'updated_at' => now(),
        ]);

        $this->command->info('Created sample location data:');
        $this->command->info('  ✓ United States > California > Los Angeles');
        $this->command->info('  ✓ Nigeria > Lagos > Lagos');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['country_name' => 'Pakistan', 'country_code' => 'PK'],
            ['country_name' => 'United States', 'country_code' => 'US'],
            ['country_name' => 'United Kingdom', 'country_code' => 'UK'],
            ['country_name' => 'Canada', 'country_code' => 'CA'],
            ['country_name' => 'Australia', 'country_code' => 'AU'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
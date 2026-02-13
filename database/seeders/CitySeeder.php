<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        // Get Pakistan's ID
        $pakistanId = Country::where('country_code', 'PK')->first()->id;

        $pakistanCities = [
            ['name' => 'Karachi', 'country_id' => $pakistanId],
            ['name' => 'Lahore', 'country_id' => $pakistanId],
            ['name' => 'Islamabad', 'country_id' => $pakistanId],
            ['name' => 'Rawalpindi', 'country_id' => $pakistanId],
            ['name' => 'Faisalabad', 'country_id' => $pakistanId],
            ['name' => 'Multan', 'country_id' => $pakistanId],
            ['name' => 'Peshawar', 'country_id' => $pakistanId],
            ['name' => 'Quetta', 'country_id' => $pakistanId],
            ['name' => 'Sialkot', 'country_id' => $pakistanId],
            ['name' => 'Gujranwala', 'country_id' => $pakistanId],
        ];

        foreach ($pakistanCities as $city) {
            City::create($city);
        }

        // Get US ID
        $usId = Country::where('country_code', 'US')->first()->id;

        $usCities = [
            ['name' => 'New York', 'country_id' => $usId],
            ['name' => 'Los Angeles', 'country_id' => $usId],
            ['name' => 'Chicago', 'country_id' => $usId],
            ['name' => 'Houston', 'country_id' => $usId],
            ['name' => 'Phoenix', 'country_id' => $usId],
        ];

        foreach ($usCities as $city) {
            City::create($city);
        }

        // Add more cities for other countries as needed
    }
}
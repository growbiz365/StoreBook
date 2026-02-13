<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Seeder;

class TimezoneSeeder extends Seeder
{
    public function run()
    {
        $timezones = [
            ['timezone_name' => 'Asia/Karachi', 'utc_offset' => '05'],
            ['timezone_name' => 'UTC', 'utc_offset' => '+00'],
            ['timezone_name' => 'America/New_York', 'utc_offset' => '-05'],
            ['timezone_name' => 'Europe/London', 'utc_offset' => '+00'],
            ['timezone_name' => 'Asia/Dubai', 'utc_offset' => '+04'],
            ['timezone_name' => 'Asia/Singapore', 'utc_offset' => '+08'],
            ['timezone_name' => 'Australia/Sydney', 'utc_offset' => '+11'],
            ['timezone_name' => 'Pacific/Auckland', 'utc_offset' => '+13'],
        ];

        foreach ($timezones as $timezone) {
            Timezone::create($timezone);
        }
    }
}
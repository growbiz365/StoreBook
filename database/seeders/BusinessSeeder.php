<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Package;
use App\Models\Currency;
use App\Models\Country;
use App\Models\City;
use App\Models\Timezone;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        // Get necessary IDs
        $pkrId = Currency::where('currency_code', 'PKR')->first()->id;
        $pakistanId = Country::where('country_code', 'PK')->first()->id;
        $karachiId = City::where('name', 'Karachi')->first()->id;
        $timezoneId = Timezone::where('timezone_name', 'Asia/Karachi')->first()->id;
        $packageId = Package::where('package_name', 'Standard Package')->first()->id;

        $businesses = [
            [
                'business_name' => 'Tech Solutions Inc.',
                'owner_name' => 'Ahmed Khan',
                'cnic' => '35201-1234567-1',
                'contact_no' => '+92-300-1234567',
                'email' => 'ahmed@techsolutions.com',
                'address' => '123 Main Street, Gulberg III, Lahore, Pakistan',
                'country_id' => $pakistanId,
                'timezone_id' => $timezoneId,
                'currency_id' => $pkrId,
                'package_id' => $packageId,
                'date_format' => 'Y-m-d',
            ],
        ];

        foreach ($businesses as $business) {
            Business::create($business);
        }
    }
}

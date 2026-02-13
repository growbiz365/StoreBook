<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        // Get PKR currency ID
        $pkrId = Currency::where('currency_code', 'PKR')->first()->id;

        $packages = [
            [
                'package_name' => 'Standard Package',
                'description' => 'Complete solution for medium-sized businesses',
                'price' => 10000,
                'currency_id' => $pkrId,
                'duration_months' => 12,
            ],
            [
                'package_name' => 'Premium Package',
                'description' => 'Advanced features for large Businesses',
                'price' => 15000,
                'currency_id' => $pkrId,
                'duration_months' => 12,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
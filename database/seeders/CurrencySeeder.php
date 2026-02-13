<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            [
                'currency_name' => 'Pakistani Rupee',
                'currency_code' => 'PKR',
                'symbol' => 'Rs',
            ],
            [
                'currency_name' => 'US Dollar',
                'currency_code' => 'USD',
                'symbol' => '$',
            ],
            [
                'currency_name' => 'Euro',
                'currency_code' => 'EUR',
                'symbol' => '€',
            ],
            [
                'currency_name' => 'British Pound',
                'currency_code' => 'GBP',
                'symbol' => '£',
            ],
            [
                'currency_name' => 'UAE Dirham',
                'currency_code' => 'AED',
                'symbol' => 'د.إ',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
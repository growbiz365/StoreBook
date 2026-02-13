<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessUserSeeder extends Seeder
{
    public function run()
    {
        // Get the user by email
        $user = User::where('email', 'muhammad.irshad.dev@gmail.com')->first();

        // Get the business by name
        $business = Business::where('business_name', 'Tech Solutions Inc.')->first();

        if (!$user) {
            $this->command->error("User not found.");
            return;
        }

        if (!$business) {
            $this->command->error("Business not found.");
            return;
        }

        // Attach the business to the user
        $user->businesses()->sync([$business->id]);
        $this->command->info("Business '{$business->business_name}' attached to user '{$user->email}'.");
    }
}

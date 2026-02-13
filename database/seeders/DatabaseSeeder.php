<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RolesSeeder::class,
            PermissionSeeder::class,
            AssignPermissionsSeeder::class,
            CountrySeeder::class,
            CitySeeder::class,
            CurrencySeeder::class,
            TimezoneSeeder::class,
            PackageSeeder::class,
            PackageModuleSeeder::class,
            BusinessSeeder::class,
            
            


        ]);

        // Create a user and assign the "admin" role
        $adminUser = User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'irshad', // Add this line
            'email' => 'muhammad.irshad.dev@gmail.com',
        ]);
        $adminUser->assignRole('Super Admin');

        // Create a regular user
        $user = User::factory()->create([
            'name' => 'public user',
            'username' => 'user', // Add this line
            'email' => 'ghufran_javed@yahoo.com',
        ]);
        $user->assignRole('user');


        $this->call([
            BusinessUserSeeder::class,
            ChartOfAccountsSeeder::class,

        ]);

    }
}

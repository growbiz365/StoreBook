<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // i am not calling role factory, i am generating/creating roles directly here
        // Create default roles if they do not exist
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    }
}

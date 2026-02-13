<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PackageModuleSeeder extends Seeder
{
    public function run()
    {
        // Get the Standard Package
        $package = Package::where('package_name', 'Standard Package')->first();

        // Get all the required permission IDs
        $permissionNames = [
            'view dashboard',
            'view settings',

            'view permissions',
            'edit permissions',
            'create permissions',
            'delete permissions',

            'view users',
            'create users',
            'edit users',
            'delete users',

            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            'view countries',
            'edit countries',
            'create countries',
            'delete countries',

            
            'view timezones',
            'create timezones',
            'edit timezones',
            'delete timezones',

            'view currencies',
            'create currencies',
            'edit currencies',
            'delete currencies',

            'view cities',
            'create cities',
            'edit cities',
            'delete cities',

            'view packages',
            'create packages',
            'edit packages',
            'delete packages',
            'assign modules',


            'view subusers',
            'create subusers',
            'edit subusers',
            'delete subusers',


            'view businesses',
            'create businesses',
            'edit businesses',
            'delete businesses',


        ];

        // Get permission IDs
        $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();

        // Sync the permissions with the package
        $package->modules()->sync($permissionIds);
    }
}

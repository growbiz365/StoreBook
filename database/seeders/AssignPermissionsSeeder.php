<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Fetch existing roles
        $adminRole = Role::where('name', 'Super Admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Define permissions for each role
        $adminPermissions = [
            'view dashboard',
            'view settings',

            'view user management',

            'view permissions',
            'edit permissions',
            'create permissions',
            'delete permissions',

            'view users',
            'create users',
            'edit users',
            

            'view roles',
            'create roles',
            'edit roles',
            

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
            'assign modules',


            'view subusers',
            'create subusers',
            'edit subusers',
            'delete subusers',


            'view businesses',
            'create businesses',
            'edit businesses',
            'delete businesses',

            // Arms permissions disabled - StoreBook is items-only
            // 'view arm_types',
            // 'create arm_types',
            // 'edit arm_types',
            // 'delete arm_types',

            // 'view arm_categories',
            // 'create arm_categories',
            // 'edit arm_categories',
            // 'delete arm_categories',

            // 'view arm_makes',
            // 'create arm_makes',
            // 'edit arm_makes',
            // 'delete arm_makes',

            // 'view arm_calibers',
            // 'create arm_calibers',
            // 'edit arm_calibers',
            // 'delete arm_calibers',

            // 'view arm_conditions',
            // 'create arm_conditions',
            // 'edit arm_conditions',
            // 'delete arm_conditions',

            'view finance',

            'view account types',

            'view journal entries',

            'view chart of accounts',
            'create chart of accounts',
            'edit chart of accounts',
            'delete chart of accounts',

            // 'view arms',
            // 'create arms',
            // 'edit arms',
            // 'view arms report',
            // 'view arms opening stock',
            // 'view arms stock ledger',
            // 'view arms history',

            'view items',
            'create items',
            'edit items',
            'delete items',
            'view items stock ledger',

            'view item-types',
            'create item-types',
            'edit item-types',
            'delete item-types',

            'view parties',
            'create parties',
            'edit parties',
            'delete parties',
            'view parties ledger',
            'view parties balances',
            'view parties transfers',
            'create parties transfers',
            'edit parties transfers',
            'delete parties transfers',



            'view banks',
            'create banks',
            'edit banks',
            'delete banks',
            'view bank-transfers',
            'create bank-transfers',
            'edit bank-transfers',
            'delete bank-transfers',
            'view bank ledger',
            'view bank balances',


            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'view expenses report',

            'view expense heads',
            'create expense heads',
            'edit expense heads',
            'delete expense heads',



            'view general vouchers',
            'create general vouchers',
            'edit general vouchers',
            'delete general vouchers',

            'view purchases',
            'create purchases',
            'edit purchases',
            'cancel purchases',

            'view purchase returns',
            'create purchase returns',
            'edit purchase returns',
            'cancel purchase returns',
            
            'view sales',
            'create sales',
            'edit sales',
            'cancel sales',

            'view sale returns',
            'create sale returns',
            'edit sale returns',
            'cancel sale returns',

            'view other incomes',
            'create other incomes',
            'edit other incomes',
            'delete other incomes',

            'view income heads',
            'create income heads',
            'edit income heads',
            'delete income heads',

            'view balance sheet',
            'view profit-loss-report',
            'view invoice-profit-loss-report',
            'view trial-balance',
            'view general ledger',
            'view detailed-general-ledger',
            'create quotations',
            'edit quotations',
            'delete quotations',
            'view quotations',







        ];

        $userPermissions = [
            'view dashboard',
            'view settings',

            'view user management',

            
            'view balance sheet',
            'view profit-loss-report',
            'view invoice-profit-loss-report',
            'view trial-balance',
            'view general ledger',
            'create quotations',
            'edit quotations',
            'delete quotations',
            'view quotations',
            'view detailed-general-ledger',

            

            


            'view subusers',
            'create subusers',
            'edit subusers',
            


            'view businesses',
            
            'edit businesses',
            

            // Arms permissions disabled - StoreBook is items-only
            // 'view arm_types',
            // 'create arm_types',
            // 'edit arm_types',
            // 'delete arm_types',

            // 'view arm_categories',
            // 'create arm_categories',
            // 'edit arm_categories',
            // 'delete arm_categories',

            // 'view arm_makes',
            // 'create arm_makes',
            // 'edit arm_makes',
            // 'delete arm_makes',

            // 'view arm_calibers',
            // 'create arm_calibers',
            // 'edit arm_calibers',
            // 'delete arm_calibers',

            // 'view arm_conditions',
            // 'create arm_conditions',
            // 'edit arm_conditions',
            // 'delete arm_conditions',

            'view finance',

            'view account types',

            'view journal entries',

            'view chart of accounts',
            'create chart of accounts',
            'edit chart of accounts',
            'delete chart of accounts',

            // 'view arms',
            // 'create arms',
            // 'edit arms',
            // 'view arms report',
            // 'view arms opening stock',
            // 'view arms stock ledger',
            // 'view arms history',

            'view items',
            'create items',
            'edit items',
            'delete items',
            

            'view item-types',
            'create item-types',
            'edit item-types',
            'delete item-types',

            'view parties',
            'create parties',
            'edit parties',
            'delete parties',
            'view parties ledger',
            'view parties balances',
            'view parties transfers',
            'create parties transfers',
            'edit parties transfers',
            'delete parties transfers',



            'view banks',
            'create banks',
            'edit banks',
            'delete banks',
            'view bank-transfers',
            'create bank-transfers',
            'edit bank-transfers',
            'delete bank-transfers',
            'view bank ledger',
            'view bank balances',


            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'view expenses report',

            'view expense heads',
            'create expense heads',
            'edit expense heads',
            'delete expense heads',



            'view general vouchers',
            'create general vouchers',
            'edit general vouchers',
            'delete general vouchers',

            'view purchases',
            'create purchases',
            'edit purchases',
            'cancel purchases',

            'view purchase returns',
            'create purchase returns',
            'edit purchase returns',
            'cancel purchase returns',
            
            'view sales',
            'create sales',
            'edit sales',
            'cancel sales',

            'view sale returns',
            'create sale returns',
            'edit sale returns',
            'cancel sale returns',

            'view other incomes',
            'create other incomes',
            'edit other incomes',
            'delete other incomes',

            'view income heads',
            'create income heads',
            'edit income heads',
            'delete income heads',
        ];

        // Assign permissions to the admin role
        if ($adminRole) {
            foreach ($adminPermissions as $permission) {
                $perm = Permission::firstWhere('name', $permission);
                if ($perm && !$adminRole->hasPermissionTo($perm)) {
                    $adminRole->givePermissionTo($perm);
                }
            }
        }

        // Assign permissions to the user role
        if ($userRole) {
            foreach ($userPermissions as $permission) {
                $perm = Permission::firstWhere('name', $permission);
                if ($perm && !$userRole->hasPermissionTo($perm)) {
                    $userRole->givePermissionTo($perm);
                }
            }
        }
    }
}

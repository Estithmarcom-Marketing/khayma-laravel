<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /**
         * 1️⃣ All permissions used in routes
         */
        $permissions = [
   
            'store-admin',

     
            'read-city',
            'store-city',
            'delete-city',

            
            'read-city-shipment',
            'store-city-shipment',
            'delete-city-shipment',

            'read-colors',
            'store-color',
            'delete-color',

      
            'read-sizes',
            'store-size',
            'delete-size',


            'read-brands',
            'store-brand',
            'delete-brand',

        
            'read-promo-codes',
            'store-promo-code',
            'delete-promo-code',

            
            'read-delivery-methods',
            'store-delivery-method',
            'delete-delivery-method',

        
            'read-payment-methods',
            'store-payment-method',
            'delete-payment-method',

            
            'read-payment-gateways',
            'store-payment-gateway',
            'delete-payment-gateway',

          
            'read-categories',
            'store-category',
            'delete-category',

            'read-properties',
            'store-property',
            'delete-property',

            'read-products',
            'store-product',
            'delete-product',

    
            'read-questions',
            'store-question',
            'delete-question',
        ];

  
        Permission::whereNotIn('name', $permissions)->delete();

        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

       
        $roles = [
            'super-admin' => $permissions,

            'data-entry' => [
                'read-city',
                'read-brands',
                'read-categories',
                'read-products',
                'store-product',
                'store-category',
                'store-brand',
                'store-city',
            ],

            'inventory-manager' => [
                'read-products',
                'store-product',
                'delete-product',
                'read-categories',
                'store-category',
                'delete-category',
            ],

            'accountant' => [
                'read-payment-methods',
                'read-payment-gateways',
                'read-promo-codes',
            ],
        ];

       
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('Roles and permissions updated successfully.');
    }
}

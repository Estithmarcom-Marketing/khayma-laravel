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
            // Admin
            'store-admin',

            // Cities
            'read-city',
            'store-city',
            'delete-city',

            // City Shipments
            'read-city-shipment',
            'store-city-shipment',
            'delete-city-shipment',

            // Colors
            'read-colors',
            'store-color',
            'delete-color',

            // Sizes
            'read-sizes',
            'store-size',
            'delete-size',

            // Brands
            'read-brands',
            'store-brand',
            'delete-brand',

            // Promo Codes
            'read-promo-codes',
            'store-promo-code',
            'delete-promo-code',

            // Delivery Methods
            'read-delivery-methods',
            'store-delivery-method',
            'delete-delivery-method',

            // Payment Methods
            'read-payment-methods',
            'store-payment-method',
            'delete-payment-method',

            // Payment Gateways
            'read-payment-gateways',
            'store-payment-gateway',
            'delete-payment-gateway',

            // Categories
            'read-categories',
            'store-category',
            'delete-category',

            // Properties
            'read-properties',
            'store-property',
            'delete-property',

            // Products
            'read-products',
            'store-product',
            'delete-product',

            // Questions
            'read-questions',
            'store-question',
            'delete-question',
        ];

        /**
         * 2️⃣ Remove old permissions
         */
        Permission::whereNotIn('name', $permissions)->delete();

        /**
         * 3️⃣ Create permissions
         */
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * 4️⃣ Roles & permissions
         */
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

        /**
         * 5️⃣ Sync roles
         */
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('Roles and permissions updated successfully.');
    }
}

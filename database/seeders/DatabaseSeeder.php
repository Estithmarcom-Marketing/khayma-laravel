<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@alkhimah.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('4s65dASFa6#$@dda4'),
            ]
        );
        $role = Role::where('name', 'super-admin')->first();
        $admin->assignRole($role);
        $this->call(CitySeeder::class);
        $this->call(CityShipmentSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(SizeSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(DeliveryMethodSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(PaymentGatewaySeeder::class);
        $this->call(BannerSeeder::class);
        // Category::factory()->count(5)->create();
        // Brand::factory()->count(10)->create();
        // Product::factory()->count(500)->create();
        $this->call(ProductVariationSeeder::class);
    }
}

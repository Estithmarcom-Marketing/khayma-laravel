<?php

namespace Database\Seeders;

use App\Models\User;
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
        $user = User::firstOrCreate(
            ['email' => 'mahmoudelsayed@gmail.com'],
            [
                'name' => 'Mahmoud Elsayed',
                'password' => Hash::make('12345678'),
                'is_guest' => false,
            ]
        );
        $role = Role::where('name', 'super-admin')->first();
        $user->assignRole($role);
        $this->call(CitySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(SizeSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductVariationSeeder::class);
        $this->call(DeliveryMethodSeeder::class);
        $this->call(PaymentMethodSeeder::class);
    }
}

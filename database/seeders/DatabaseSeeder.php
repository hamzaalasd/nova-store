<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'phone' => '777777777',
                'password' => 'password',
                'type' => 'customer',
                'status' => 'active',
            ]
        );

        $this->call([
            CurrencySeeder::class,
            ProductGroupSeeder::class,
            ShippingMethodSeeder::class,
            PaymentMethodSeeder::class,
            SettingSeeder::class,
            DemoStoreSeeder::class,
            NovaExperienceSeeder::class,
        ]);
    }
}

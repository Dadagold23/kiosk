<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            MarketplaceProviderSeeder::class,
            GlobalMarketplaceSeeder::class,
            AmerceAssetProductSeeder::class,
            AmerceDisplayAssetProductSeeder::class,
            EmergencyServiceUnitSeeder::class,
            OperationalDataSeeder::class,
            DataSeeder::class,
        ]);
    }
}

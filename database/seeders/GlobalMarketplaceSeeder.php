<?php

namespace Database\Seeders;

use App\Services\MarketplaceCatalogSyncService;
use Illuminate\Database\Seeder;

class GlobalMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        app(MarketplaceCatalogSyncService::class)->sync(
            seedOnly: true,
            pruneMissing: true
        );
    }
}

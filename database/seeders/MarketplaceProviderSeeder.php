<?php

namespace Database\Seeders;

use App\Models\MarketplaceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MarketplaceProviderSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('marketplace_providers')) {
            return;
        }

        $providers = config('kiosk.marketplaces.providers', []);

        foreach ($providers as $key => $config) {
            MarketplaceProvider::updateOrCreate(
                ['provider_key' => $key],
                [
                    'label' => $config['label'] ?? ucfirst($key),
                    'enabled' => (bool) ($config['enabled'] ?? false),
                    'feed_url' => $config['feed_url'] ?? null,
                ]
            );
        }
    }
}

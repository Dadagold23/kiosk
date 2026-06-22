<?php

namespace Tests\Feature;

use App\Models\MarketplaceSyncRun;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MarketplaceCatalogSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_sync_command_imports_global_products_from_seed_feeds(): void
    {
        $this->seed(CategorySeeder::class);

        Artisan::call('marketplaces:sync --seed-only --prune-missing');

        $this->assertSame(5, MarketplaceSyncRun::count());
        $this->assertSame(25, Product::query()->where('source_type', 'global')->count());

        $this->assertDatabaseHas('products', [
            'sku' => 'JUM-PHN-001',
            'source_type' => 'global',
            'source_marketplace' => 'Jumia',
            'status' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'TEM-BABY-001',
            'source_type' => 'global',
            'source_marketplace' => 'Temu',
            'status' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'ALI-COMP-001',
            'source_type' => 'global',
            'source_marketplace' => 'Alibaba',
            'status' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'AEX-HOME-001',
            'source_type' => 'global',
            'source_marketplace' => 'AliExpress',
            'status' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'JIJ-PHN-001',
            'source_type' => 'global',
            'source_marketplace' => 'Jiji',
            'status' => true,
        ]);
    }
}

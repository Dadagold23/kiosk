<?php

namespace App\Services;

use App\Models\Category;
use App\Models\MarketplaceProvider;
use App\Models\MarketplaceSyncRun;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MarketplaceCatalogSyncService
{
    private const SEEDED_ITEMS_PER_PROVIDER = 5;

    public function sync(?array $providers = null, bool $seedOnly = false, bool $pruneMissing = false): array
    {
        $providersToSync = $this->providers($providers);
        if ($providersToSync === []) {
            return [];
        }
        $categories = Category::query()
            ->where('type', 'product')
            ->get()
            ->keyBy('slug');

        $results = [];

        foreach ($providersToSync as $provider => $config) {
            $run = MarketplaceSyncRun::create([
                'provider' => $provider,
                'status' => 'running',
                'feed_url' => $config['feed_url'] ?: null,
                'started_at' => now(),
            ]);

            try {
                [$items, $source] = $this->loadItems($config, $seedOnly);
                $summary = $this->importProvider($provider, $config, $items, $categories, $pruneMissing);

                $run->update([
                    'source' => $source,
                    'status' => 'completed',
                    'items_seen' => $summary['seen'],
                    'items_created' => $summary['created'],
                    'items_updated' => $summary['updated'],
                    'items_deactivated' => $summary['deactivated'],
                    'finished_at' => now(),
                    'meta' => [
                        'skipped' => $summary['skipped'],
                    ],
                ]);

                $results[] = array_merge([
                    'provider' => $provider,
                    'source' => $source,
                    'status' => 'completed',
                ], $summary);
            } catch (\Throwable $exception) {
                Log::warning('Marketplace sync failed', [
                    'provider' => $provider,
                    'message' => $exception->getMessage(),
                ]);

                $run->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'error_message' => $exception->getMessage(),
                ]);

                $results[] = [
                    'provider' => $provider,
                    'source' => 'failed',
                    'status' => 'failed',
                    'seen' => 0,
                    'created' => 0,
                    'updated' => 0,
                    'deactivated' => 0,
                    'skipped' => 0,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function providers(?array $providers = null): array
    {
        $configured = config('kiosk.marketplaces.providers', []);
        $configured = $this->applyProviderOverrides($configured);

        if ($providers === null || $providers === []) {
            return array_filter($configured, fn (array $config) => (bool) ($config['enabled'] ?? false));
        }

        $requested = collect($providers)
            ->filter()
            ->map(fn ($provider) => strtolower((string) $provider))
            ->values()
            ->all();

        return array_filter(
            $configured,
            fn (array $config, string $provider) => in_array($provider, $requested, true) && (bool) ($config['enabled'] ?? false),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function applyProviderOverrides(array $configured): array
    {
        if (! Schema::hasTable('marketplace_providers')) {
            return $configured;
        }

        $overrides = MarketplaceProvider::query()
            ->get()
            ->keyBy('provider_key');

        if ($overrides->isEmpty()) {
            return $configured;
        }

        foreach ($configured as $key => $config) {
            if (! $overrides->has($key)) {
                continue;
            }

            $record = $overrides->get($key);
            $configured[$key]['enabled'] = $record->enabled;
            $configured[$key]['label'] = $record->label ?: ($config['label'] ?? ucfirst($key));
            $configured[$key]['feed_url'] = $record->feed_url ?: ($config['feed_url'] ?? null);
        }

        return $configured;
    }

    private function loadItems(array $config, bool $seedOnly): array
    {
        if (! $seedOnly && filled($config['feed_url'] ?? null)) {
            if (! $this->isSafeFeedUrl((string) $config['feed_url'])) {
                throw new \RuntimeException('Marketplace feed URL failed safety checks.');
            }

            try {
                $response = Http::timeout((int) config('kiosk.marketplaces.sync.timeout_seconds', 20))
                    ->retry(2, 500)
                    ->withHeaders([
                        'User-Agent' => 'KioskCatalogSync/1.0',
                    ])
                    ->acceptJson()
                    ->get($config['feed_url']);

                $response->throw();

                return [$this->extractItems($response->json()), 'remote'];
            } catch (\Throwable $exception) {
                if (! filled($config['seed_path'] ?? null)) {
                    throw $exception;
                }

                return [$this->limitSeedItems($this->extractItems($this->readJsonFile($config['seed_path']))), 'seed_fallback'];
            }
        }

        return [$this->limitSeedItems($this->extractItems($this->readJsonFile($config['seed_path'] ?? ''))), 'seed'];
    }

    private function extractItems(array $payload): array
    {
        $items = Arr::get($payload, 'items', $payload);

        return is_array($items) ? array_values($items) : [];
    }

    private function limitSeedItems(array $items): array
    {
        return array_slice($items, 0, self::SEEDED_ITEMS_PER_PROVIDER);
    }

    private function readJsonFile(string $path): array
    {
        if ($path === '' || ! is_file($path)) {
            throw new \RuntimeException('Marketplace seed/feed file could not be found: ' . $path);
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException('Marketplace seed/feed file is not valid JSON: ' . $path);
        }

        return $decoded;
    }

    private function importProvider(
        string $provider,
        array $config,
        array $items,
        Collection $categories,
        bool $pruneMissing
    ): array {
        $seen = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $processedSkus = [];
        $label = (string) ($config['label'] ?? ucfirst($provider));

        foreach ($items as $item) {
            $normalized = $this->normalizeItem($item, $categories, $label);

            if ($normalized === null) {
                $skipped++;
                continue;
            }

            $seen++;
            $processedSkus[] = $normalized['sku'];
            $exists = Product::query()->where('sku', $normalized['sku'])->exists();

            Product::updateOrCreate(
                ['sku' => $normalized['sku']],
                $normalized
            );

            if ($exists) {
                $updated++;
            } else {
                $created++;
            }
        }

        $deactivated = 0;

        if ($pruneMissing && $processedSkus !== []) {
            $deactivated = Product::query()
                ->where('source_type', 'global')
                ->where('source_marketplace', $label)
                ->whereNotIn('sku', $processedSkus)
                ->where('status', true)
                ->update(['status' => false]);
        }

        return [
            'seen' => $seen,
            'created' => $created,
            'updated' => $updated,
            'deactivated' => $deactivated,
            'skipped' => $skipped,
        ];
    }

    private function normalizeItem(array $item, Collection $categories, string $marketplaceLabel): ?array
    {
        $category = $categories->get((string) ($item['category_slug'] ?? ''));
        $name = trim((string) ($item['name'] ?? ''));
        $sku = trim((string) ($item['sku'] ?? ''));
        $price = $item['price'] ?? null;

        if (! $category || $name === '' || $sku === '' || ! is_numeric($price)) {
            return null;
        }

        $salePrice = $item['sale_price'] ?? null;
        $quantity = $item['quantity'] ?? 0;
        $image = filled($item['image'] ?? null) ? (string) $item['image'] : null;
        $externalUrl = filled($item['external_url'] ?? null) ? (string) $item['external_url'] : null;

        return [
            'category_id' => $category->id,
            'source_type' => 'global',
            'source_marketplace' => $marketplaceLabel,
            'sku' => $sku,
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name . '-' . $sku),
            'description' => (string) ($item['description'] ?? ''),
            'price' => (float) $price,
            'sale_price' => is_numeric($salePrice) ? (float) $salePrice : null,
            'quantity' => is_numeric($quantity) ? max((int) $quantity, 0) : 0,
            'image' => $image,
            'external_url' => $externalUrl,
            'featured' => (bool) ($item['featured'] ?? false),
            'status' => array_key_exists('status', $item) ? (bool) $item['status'] : true,
        ];
    }

    private function isSafeFeedUrl(string $url): bool
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https') {
            return false;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($host === '' || $host === 'localhost') {
            return false;
        }

        if (preg_match('/^(127\.|10\.|192\.168\.|172\.(1[6-9]|2\d|3[0-1])\.)/', $host)) {
            return false;
        }

        return true;
    }
}

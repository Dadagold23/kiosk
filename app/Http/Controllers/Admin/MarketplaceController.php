<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceSyncRun;
use App\Models\MarketplaceProvider;
use App\Models\Product;
use App\Services\MarketplaceCatalogSyncService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $providers = $this->providerCollection();

        $productsQuery = Product::query()
            ->where('source_type', 'global')
            ->when($request->filled('provider'), function ($query) use ($request) {
                $label = $this->providerLabel($request->string('provider')->toString());
                if ($label) {
                    $query->where('source_marketplace', $label);
                }
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString() === 'active');
            })
            ->latest();

        $products = $productsQuery->paginate(15)->withQueryString();

        $runs = MarketplaceSyncRun::query()
            ->latest('started_at')
            ->paginate(10, ['*'], 'runs_page')
            ->withQueryString();

        $summary = [
            'total_global' => Product::where('source_type', 'global')->count(),
            'active_global' => Product::where('source_type', 'global')->where('status', true)->count(),
            'inactive_global' => Product::where('source_type', 'global')->where('status', false)->count(),
            'last_run' => MarketplaceSyncRun::latest('started_at')->first(),
        ];

        return view('admin.marketplaces.index', compact('providers', 'products', 'runs', 'summary'));
    }

    public function sync(Request $request, MarketplaceCatalogSyncService $syncService)
    {
        $validated = $request->validate([
            'provider' => ['nullable', 'string', Rule::in($this->providerKeys())],
            'seed_only' => ['nullable', 'boolean'],
            'prune_missing' => ['nullable', 'boolean'],
        ]);

        $providers = [];
        if (! empty($validated['provider'])) {
            $providers = [$validated['provider']];
        }

        $results = $syncService->sync(
            providers: $providers,
            seedOnly: (bool) ($validated['seed_only'] ?? false),
            pruneMissing: (bool) ($validated['prune_missing'] ?? true)
        );

        if ($results === []) {
            return back()->with('error', 'No enabled marketplace providers matched the selected filter.');
        }

        $failed = collect($results)->contains(fn (array $result) => $result['status'] === 'failed');
        $message = $failed
            ? 'Marketplace sync completed with some failures. Review the sync log below.'
            : 'Marketplace sync completed successfully.';

        return back()->with($failed ? 'warning' : 'success', $message);
    }

    public function updateProductStatus(Request $request, Product $product)
    {
        if ($product->source_type !== 'global') {
            return back()->with('error', 'Only global marketplace items can be updated here.');
        }

        $validated = $request->validate([
            'action' => ['required', Rule::in(['deactivate', 'restore'])],
        ]);

        $nextStatus = $validated['action'] === 'restore';
        $product->update(['status' => $nextStatus]);

        return back()->with('success', $nextStatus ? 'Marketplace item restored.' : 'Marketplace item deactivated.');
    }

    public function toggleProvider(Request $request, string $providerKey)
    {
        if (! Schema::hasTable('marketplace_providers')) {
            return back()->with('error', 'Marketplace providers table is not available yet.');
        }

        $provider = MarketplaceProvider::query()->where('provider_key', $providerKey)->first();

        if (! $provider) {
            return back()->with('error', 'Marketplace provider was not found.');
        }

        $provider->update(['enabled' => ! $provider->enabled]);

        return back()->with('success', $provider->enabled ? 'Marketplace provider enabled.' : 'Marketplace provider disabled.');
    }

    private function providerLabel(string $providerKey): ?string
    {
        $providers = config('kiosk.marketplaces.providers', []);
        if (! isset($providers[$providerKey])) {
            return null;
        }

        return (string) ($providers[$providerKey]['label'] ?? $providerKey);
    }

    private function providerCollection()
    {
        $configured = collect(config('kiosk.marketplaces.providers', []))
            ->map(function (array $config, string $key) {
                return array_merge($config, ['key' => $key]);
            });

        if (! Schema::hasTable('marketplace_providers')) {
            return $configured->values();
        }

        $records = MarketplaceProvider::query()->get()->keyBy('provider_key');

        if ($records->isEmpty()) {
            return $configured->values();
        }

        return $configured->map(function (array $config, string $key) use ($records) {
            if (! $records->has($key)) {
                return $config;
            }

            $record = $records->get($key);
            $config['label'] = $record->label ?: ($config['label'] ?? ucfirst($key));
            $config['enabled'] = (bool) $record->enabled;
            $config['feed_url'] = $record->feed_url ?: ($config['feed_url'] ?? null);

            return $config;
        })->values();
    }

    private function providerKeys(): array
    {
        return collect(config('kiosk.marketplaces.providers', []))
            ->keys()
            ->all();
    }
}

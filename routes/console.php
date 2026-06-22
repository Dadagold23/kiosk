<?php

use App\Services\MarketplaceCatalogSyncService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('marketplaces:sync {--provider=* : Limit sync to one or more marketplace keys} {--seed-only : Force bundled marketplace feed files instead of remote URLs} {--prune-missing : Deactivate global items that are missing from the current provider feed}', function (MarketplaceCatalogSyncService $syncService) {
    $providers = array_filter((array) $this->option('provider'));
    $seedOnly = (bool) $this->option('seed-only');
    $pruneMissing = (bool) config('kiosk.marketplaces.sync.prune_missing', true) || (bool) $this->option('prune-missing');

    $results = $syncService->sync(
        providers: $providers,
        seedOnly: $seedOnly,
        pruneMissing: $pruneMissing
    );

    if ($results === []) {
        $this->warn('No enabled marketplace providers matched the requested filters.');

        return self::SUCCESS;
    }

    $this->table(
        ['Provider', 'Source', 'Status', 'Seen', 'Created', 'Updated', 'Deactivated', 'Skipped'],
        collect($results)->map(fn (array $result) => [
            $result['provider'],
            $result['source'],
            $result['status'],
            $result['seen'],
            $result['created'],
            $result['updated'],
            $result['deactivated'],
            $result['skipped'],
        ])->all()
    );

    if (collect($results)->contains(fn (array $result) => $result['status'] === 'failed')) {
        return self::FAILURE;
    }

    return self::SUCCESS;
})->purpose('Sync global marketplace catalog feeds into the products table');

$syncEvent = Schedule::command('marketplaces:sync --prune-missing')
    ->withoutOverlapping()
    ->when(fn (): bool => (bool) config('kiosk.marketplaces.sync.enabled', true));

$schedule = (string) config('kiosk.marketplaces.sync.schedule', 'everySixHours');

match ($schedule) {
    'everyThirtyMinutes' => $syncEvent->everyThirtyMinutes(),
    'hourly' => $syncEvent->hourly(),
    'daily' => $syncEvent->daily(),
    'twiceDaily' => $syncEvent->twiceDaily(),
    default => $syncEvent->everySixHours(),
};

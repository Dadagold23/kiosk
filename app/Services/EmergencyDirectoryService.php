<?php

namespace App\Services;

use App\Models\EmergencyRequest;
use App\Models\EmergencyServiceUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EmergencyDirectoryService
{
    public function getGeoPayload(): array
    {
        $path = (string) config('kiosk.emergency.geo_data_path');

        if (! File::exists($path)) {
            return [
                'country' => [
                    'name' => (string) config('kiosk.emergency.default_country_name', 'Nigeria'),
                    'code' => (string) config('kiosk.emergency.default_country_code', 'NG'),
                ],
                'states' => [],
            ];
        }

        return json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getFrontendPayload(): array
    {
        $geo = $this->getGeoPayload();
        $units = $this->getDirectoryCollection();

        return [
            'country' => $geo['country'] ?? [
                'name' => (string) config('kiosk.emergency.default_country_name', 'Nigeria'),
                'code' => (string) config('kiosk.emergency.default_country_code', 'NG'),
            ],
            'states' => $geo['states'] ?? [],
            'national_units' => $units->where('is_national', true)->values()->all(),
            'units_by_state' => $units
                ->where('is_national', false)
                ->groupBy(fn (array $unit) => $unit['state_name'])
                ->map(fn (Collection $group) => $group->values()->all())
                ->all(),
        ];
    }

    public function getDirectoryCollection(): Collection
    {
        $units = EmergencyServiceUnit::query()
            ->orderByDesc('is_national')
            ->orderBy('state_name')
            ->orderBy('unit_name')
            ->get();

        if ($units->isNotEmpty()) {
            return $units->map(fn (EmergencyServiceUnit $unit) => $unit->toArray());
        }

        $path = (string) config('kiosk.emergency.directory_data_path');

        if (! File::exists($path)) {
            return collect();
        }

        return collect(json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR));
    }

    public function getUnitsForState(?string $stateName): Collection
    {
        return $this->getDirectoryCollection()->filter(function (array $unit) use ($stateName) {
            if ($unit['is_national']) {
                return true;
            }

            if (! filled($stateName)) {
                return false;
            }

            return $this->normalizeStateName((string) $unit['state_name']) === $this->normalizeStateName($stateName);
        })->values();
    }

    public function normalizeStateName(?string $stateName): ?string
    {
        if (! filled($stateName)) {
            return null;
        }

        $normalized = Str::of((string) $stateName)
            ->replace('-', ' ')
            ->replace('_', ' ')
            ->squish()
            ->title()
            ->value();

        return match ($normalized) {
            'Fct', 'Abuja Municipal Area Council' => 'Federal Capital Territory',
            'Akwa Ibom' => 'Akwa Ibom',
            'Cross River' => 'Cross River',
            default => $normalized,
        };
    }

    public function decorateTrackingPayload(EmergencyRequest $request): array
    {
        $request->loadMissing([
            'assignedUnit',
            'trackingEvents.emergencyServiceUnit',
            'latestTrackingEvent',
        ]);

        $events = $request->trackingEvents->sortByDesc(fn ($event) => optional($event->event_time)->timestamp ?? $event->id)
            ->values()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'status' => $event->status,
                    'location_label' => $event->location_label,
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                    'eta_minutes' => $event->eta_minutes,
                    'note' => $event->note,
                    'event_time' => optional($event->event_time)->toIso8601String(),
                    'event_time_human' => optional($event->event_time)->format('d M Y, h:i A'),
                    'unit_name' => $event->emergencyServiceUnit?->unit_name,
                    'unit_phone' => $event->emergencyServiceUnit?->contact_phone,
                ];
            });

        return [
            'request' => [
                'id' => $request->id,
                'status' => $request->status,
                'assigned_unit' => $request->assigned_unit,
                'assigned_unit_contact' => $request->assigned_unit_contact,
                'assigned_unit_toll_free' => $request->assigned_unit_toll_free,
                'dispatch_reference' => $request->dispatch_reference,
                'assigned_at' => optional($request->assigned_at)->toIso8601String(),
                'last_tracked_at' => optional($request->last_tracked_at)->toIso8601String(),
                'destination' => [
                    'location_text' => $request->location_text,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'state_name' => $request->state_name,
                    'local_government_area' => $request->local_government_area,
                ],
            ],
            'events' => $events->all(),
            'latest_event' => $events->first(),
        ];
    }
}

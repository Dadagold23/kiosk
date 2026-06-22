<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('customer.profile.edit');
    }

    public function geoOptions(): JsonResponse
    {
        $country = trim((string) request('country', ''));
        $state = (string) request('state', '');
        $geo = $this->geoDataset();

        if ($country === '') {
            return response()->json([
                'countries' => $this->countryCatalog(),
                'country' => '',
                'states' => [],
                'lgas' => [],
            ]);
        }

        if (strcasecmp($country, 'Nigeria') !== 0) {
            return response()->json([
                'countries' => $this->countryCatalog(),
                'country' => $country,
                'states' => [],
                'lgas' => [],
            ]);
        }

        $states = collect($geo['states'] ?? [])
            ->map(fn (array $item) => [
                'name' => $item['name'],
                'code' => $item['code'] ?? null,
            ])
            ->values();

        $matchingState = collect($geo['states'] ?? [])->first(function (array $item) use ($state) {
            return strcasecmp((string) ($item['name'] ?? ''), $state) === 0;
        });

        return response()->json([
            'countries' => $this->countryCatalog(),
            'country' => $country,
            'states' => $states,
            'lgas' => collect($matchingState['lgas'] ?? [])->values(),
        ]);
    }

    public function detectCountry(): JsonResponse
    {
        $validated = Validator::make(request()->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ])->validate();

        $catalog = collect($this->countryCatalog());
        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => $this->reverseGeocodeUserAgent(),
                    'Referer' => (string) config('app.url'),
                ])
                ->timeout((int) config('kiosk.geo.reverse_geocode_timeout_seconds', 8))
                ->connectTimeout((int) config('kiosk.geo.reverse_geocode_connect_timeout_seconds', 5))
                ->get((string) config('kiosk.geo.reverse_geocode_url', 'https://nominatim.openstreetmap.org/reverse'), [
                    'format' => 'jsonv2',
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'zoom' => 3,
                    'addressdetails' => 1,
                ])
                ->throw()
                ->json();
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'country' => null,
                'code' => null,
                'source' => 'gps',
            ]);
        }

        $countryName = trim((string) data_get($response, 'address.country', ''));
        $countryCode = strtoupper(trim((string) data_get($response, 'address.country_code', '')));

        $match = $catalog->first(function (array $item) use ($countryName, $countryCode) {
            if ($countryCode !== '' && strcasecmp((string) ($item['code'] ?? ''), $countryCode) === 0) {
                return true;
            }

            return $countryName !== ''
                && strcasecmp((string) ($item['name'] ?? ''), $countryName) === 0;
        });

        return response()->json([
            'country' => $match['name'] ?? ($countryName !== '' ? $countryName : null),
            'code' => $match['code'] ?? ($countryCode !== '' ? $countryCode : null),
            'source' => 'gps',
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = auth()->user();

        $emailChanged = ($validated['email'] ?? $user->email) !== $user->email;
        $isKycComplete = filled($validated['identity_type'] ?? null)
            && filled($validated['identity_number'] ?? null)
            && filled($validated['identity_country'] ?? null);

        if ($isKycComplete && blank($validated['kyc_status'] ?? null)) {
            $validated['kyc_status'] = 'pending';
        }

        if (($validated['kyc_status'] ?? null) === 'pending' && ! $user->kyc_submitted_at) {
            $validated['kyc_submitted_at'] = now();
        }

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    protected function countryCatalog(): array
    {
        $path = resource_path('data/countries.json');

        if (! is_file($path)) {
            return [
                ['name' => 'Nigeria', 'code' => 'NG'],
            ];
        }

        $decoded = json_decode(File::get($path), true);

        if (! is_array($decoded)) {
            return [
                ['name' => 'Nigeria', 'code' => 'NG'],
            ];
        }

        return collect($decoded)
            ->filter(fn ($item) => filled($item['name'] ?? null))
            ->map(fn ($item) => [
                'name' => (string) $item['name'],
                'code' => (string) ($item['code'] ?? ''),
            ])
            ->unique('name')
            ->sortBy('name')
            ->values()
            ->all();
    }

    protected function geoDataset(): array
    {
        $path = (string) config('kiosk.emergency.geo_data_path', resource_path('data/nigeria-states-lgas.json'));

        if (! is_file($path)) {
            return [
                'country' => ['name' => 'Nigeria', 'code' => 'NG'],
                'states' => [],
            ];
        }

        $decoded = json_decode(File::get($path), true);

        return is_array($decoded) ? $decoded : [
            'country' => ['name' => 'Nigeria', 'code' => 'NG'],
            'states' => [],
        ];
    }

    protected function reverseGeocodeUserAgent(): string
    {
        $appName = trim((string) config('app.name', 'Kiosk'));
        $appUrl = trim((string) config('app.url', ''));

        return $appUrl !== ''
            ? sprintf('%s Country Auto-Detect (%s)', $appName, $appUrl)
            : sprintf('%s Country Auto-Detect', $appName);
    }
}

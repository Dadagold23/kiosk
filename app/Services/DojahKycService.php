<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DojahKycService
{
    public const SANDBOX_TEST_NIN = '70123456789';

    public function isConfigured(): bool
    {
        return filled(config('kiosk.kyc.dojah.app_id'))
            && filled(config('kiosk.kyc.dojah.secret_key'));
    }

    public function verifyUser(User $user): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Dojah sandbox keys are not configured.');
        }

        $identityType = (string) $user->identity_type;
        $identityNumber = trim((string) $user->identity_number);
        $identityCountry = trim((string) ($user->identity_country ?: 'Nigeria'));

        if ($identityType === '' || $identityNumber === '') {
            throw new RuntimeException('The customer must have an identity type and identity number before verification can run.');
        }

        if (strcasecmp($identityCountry, 'Nigeria') !== 0) {
            throw new RuntimeException('This admin KYC module currently supports Nigeria lookup endpoints only.');
        }

        [$endpoint, $query] = $this->endpointFor($identityType, $identityNumber);
        $reference = 'kyc-user-' . $user->id . '-' . Str::lower((string) Str::ulid());
        $query['customer_reference'] = $reference;

        $response = $this->client()->get($endpoint, $query);
        $payload = $this->decodeResponse($response);
        $entity = $payload['entity'] ?? null;

        if (! is_array($entity) || $entity === []) {
            throw new RuntimeException('Dojah returned an empty verification payload.');
        }

        $normalized = $this->normalizeEntity($identityType, $entity);
        $checks = $this->buildChecks($user, $normalized);

        return [
            'provider' => 'dojah',
            'environment' => $this->environmentLabel(),
            'endpoint' => $endpoint,
            'reference' => $reference,
            'query' => $query,
            'entity' => $entity,
            'normalized' => $normalized,
            'checks' => $checks,
            'recommended_status' => $checks['recommended_status'],
        ];
    }

    private function endpointFor(string $identityType, string $identityNumber): array
    {
        return match ($identityType) {
            'nin' => ['/api/v1/kyc/nin', ['nin' => $identityNumber]],
            'national_id' => ['/api/v1/kyc/nin', ['nin' => $identityNumber]],
            'drivers_license' => ['/api/v1/kyc/dl', ['license_number' => $identityNumber]],
            'voters_card' => ['/api/v1/kyc/voters', ['vin' => $identityNumber]],
            default => throw new RuntimeException('The selected identity type is not supported by the current Dojah sandbox lookup flow.'),
        };
    }

    private function normalizeEntity(string $identityType, array $entity): array
    {
        return match ($identityType) {
            'nin',
            'national_id' => [
                'first_name' => $entity['first_name'] ?? null,
                'middle_name' => $entity['middle_name'] ?? null,
                'last_name' => $entity['last_name'] ?? null,
                'full_name' => trim(implode(' ', array_filter([
                    $entity['first_name'] ?? null,
                    $entity['middle_name'] ?? null,
                    $entity['last_name'] ?? null,
                ]))),
                'date_of_birth' => $entity['date_of_birth'] ?? null,
                'gender' => $entity['gender'] ?? null,
                'photo' => $entity['photo'] ?? null,
            ],
            'drivers_license' => [
                'first_name' => $entity['firstName'] ?? null,
                'middle_name' => $entity['middleName'] ?? null,
                'last_name' => $entity['lastName'] ?? null,
                'full_name' => trim(implode(' ', array_filter([
                    $entity['firstName'] ?? null,
                    $entity['middleName'] ?? null,
                    $entity['lastName'] ?? null,
                ]))),
                'date_of_birth' => $this->normalizeDate($entity['birthDate'] ?? null, 'd-m-Y'),
                'gender' => $entity['gender'] ?? null,
                'photo' => $entity['photo'] ?? null,
            ],
            'voters_card' => [
                'first_name' => $this->namePart($entity['full_name'] ?? $entity['name'] ?? null, 0),
                'middle_name' => $this->nameMiddle($entity['full_name'] ?? $entity['name'] ?? null),
                'last_name' => $this->nameLast($entity['full_name'] ?? $entity['name'] ?? null),
                'full_name' => trim((string) ($entity['full_name'] ?? $entity['name'] ?? '')),
                'date_of_birth' => $this->normalizeDate($entity['date_of_birth'] ?? $entity['dob'] ?? null),
                'gender' => $entity['gender'] ?? null,
                'photo' => $entity['photo'] ?? null,
            ],
            default => [],
        };
    }

    private function buildChecks(User $user, array $normalized): array
    {
        $userName = $this->normalizeString($user->name);
        $providerName = $this->normalizeString((string) ($normalized['full_name'] ?? ''));
        $nameMatched = $userName !== '' && $providerName !== '' && (
            $userName === $providerName
            || str_contains($providerName, $userName)
            || str_contains($userName, $providerName)
        );

        $dobMatched = null;

        if ($user->date_of_birth && filled($normalized['date_of_birth'] ?? null)) {
            $dobMatched = $user->date_of_birth->format('Y-m-d') === $this->normalizeDate($normalized['date_of_birth']);
        }

        $recommendedStatus = $nameMatched && ($dobMatched !== false)
            ? 'approved'
            : 'requires_review';

        return [
            'name_match' => $nameMatched,
            'dob_match' => $dobMatched,
            'recommended_status' => $recommendedStatus,
        ];
    }

    private function client()
    {
        return Http::baseUrl((string) config('kiosk.kyc.dojah.base_url'))
            ->acceptJson()
            ->timeout((int) config('kiosk.kyc.dojah.timeout_seconds', 20))
            ->connectTimeout((int) config('kiosk.kyc.dojah.connect_timeout_seconds', 10))
            ->withHeaders([
                'AppId' => (string) config('kiosk.kyc.dojah.app_id'),
                'Authorization' => (string) config('kiosk.kyc.dojah.secret_key'),
            ]);
    }

    private function decodeResponse(Response $response): array
    {
        $response->throw();
        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException('Invalid response received from Dojah.');
        }

        return $payload;
    }

    private function normalizeDate(?string $value, string $format = 'Y-m-d'): ?string
    {
        if (! filled($value)) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat($format, $value)->format('Y-m-d');
        } catch (\Throwable) {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
    }

    private function normalizeString(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]/', '')
            ->squish()
            ->value();
    }

    private function namePart(?string $fullName, int $index): ?string
    {
        $parts = preg_split('/\s+/', trim((string) $fullName)) ?: [];

        return $parts[$index] ?? null;
    }

    private function nameMiddle(?string $fullName): ?string
    {
        $parts = preg_split('/\s+/', trim((string) $fullName)) ?: [];

        if (count($parts) <= 2) {
            return null;
        }

        return implode(' ', array_slice($parts, 1, -1));
    }

    private function nameLast(?string $fullName): ?string
    {
        $parts = preg_split('/\s+/', trim((string) $fullName)) ?: [];

        return $parts !== [] ? end($parts) : null;
    }

    private function environmentLabel(): string
    {
        return str_contains((string) config('kiosk.kyc.dojah.base_url'), 'sandbox')
            ? 'sandbox'
            : 'production';
    }

    public function sandboxHintFor(?string $identityType): ?string
    {
        if ($this->environmentLabel() !== 'sandbox') {
            return null;
        }

        return match ($identityType) {
            'nin' => 'Dojah sandbox NIN lookup requires the official test NIN ' . self::SANDBOX_TEST_NIN . '. Real customer NIN values usually return "Wrong NIN Inputted" in sandbox.',
            'national_id' => 'Dojah sandbox NIN lookup requires the official test NIN ' . self::SANDBOX_TEST_NIN . '. Real customer NIN values usually return "Wrong NIN Inputted" in sandbox.',
            default => 'This verification is running against Dojah sandbox, so only Dojah-supported test data may work reliably.',
        };
    }
}

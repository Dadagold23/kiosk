<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SitePreferenceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user(), 403);

        $validated = $request->validate([
            'preferred_country_code' => ['nullable', 'string', 'size:2'],
            'preferred_language' => ['nullable', 'string', 'max:20'],
            'cookie_consent_mode' => ['nullable', 'string', Rule::in(['essential', 'balanced', 'accept'])],
            'cookie_consent_preferences' => ['nullable', 'array'],
            'cookie_consent_preferences.essential' => ['nullable', 'boolean'],
            'cookie_consent_preferences.analytics' => ['nullable', 'boolean'],
            'cookie_consent_preferences.marketing' => ['nullable', 'boolean'],
            'cookie_consent_preferences.saved_at' => ['nullable', 'string', 'max:60'],
        ]);

        $user = $request->user();

        if (array_key_exists('preferred_country_code', $validated)) {
            $user->preferred_country_code = strtoupper((string) ($validated['preferred_country_code'] ?? '')) ?: null;
        }

        if (array_key_exists('preferred_language', $validated)) {
            $user->preferred_language = strtolower((string) ($validated['preferred_language'] ?? '')) ?: null;
        }

        if (array_key_exists('cookie_consent_mode', $validated)) {
            $user->cookie_consent_mode = $validated['cookie_consent_mode'] ?: null;
            $user->cookie_consent_preferences = $validated['cookie_consent_preferences'] ?? null;
            $user->cookie_consent_set_at = $validated['cookie_consent_mode'] ? now() : null;
        }

        $user->save();

        return response()->json([
            'saved' => true,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Models\User;
use App\Services\DojahKycService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $user->load(['roles', 'kycVerifications.checkedBy']);

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'role' => ['nullable', 'string'],
            'kyc_status' => ['nullable', 'string', 'in:not_submitted,pending,approved,rejected,requires_review'],
            'identity_type' => ['nullable', 'string', 'in:nin,national_id,drivers_license,international_passport,voters_card,residence_permit,other'],
            'identity_number' => ['nullable', 'string', 'max:120'],
            'identity_country' => ['nullable', 'string', 'max:120'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'kyc_status' => $validated['kyc_status'] ?? null,
            'identity_type' => $validated['identity_type'] ?? null,
            'identity_number' => $validated['identity_number'] ?? null,
            'identity_country' => $validated['identity_country'] ?? null,
            'kyc_submitted_at' => in_array(($validated['kyc_status'] ?? null), ['pending', 'requires_review', 'approved'], true)
                ? ($user->kyc_submitted_at ?? now())
                : $user->kyc_submitted_at,
            'kyc_approved_at' => ($validated['kyc_status'] ?? null) === 'approved'
                ? ($user->kyc_approved_at ?? now())
                : null,
        ]);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function verifyKyc(User $user, DojahKycService $dojahKycService)
    {
        if (! $dojahKycService->isConfigured()) {
            return back()->with('error', 'Dojah sandbox keys are not configured yet. Add DOJAH_APP_ID and DOJAH_SECRET_KEY before running admin verification.');
        }

        try {
            $result = $dojahKycService->verifyUser($user);

            KycVerification::create([
                'user_id' => $user->id,
                'checked_by' => auth()->id(),
                'provider' => $result['provider'],
                'environment' => $result['environment'],
                'status' => $result['recommended_status'],
                'identity_type' => $user->identity_type,
                'identity_number_masked' => $this->maskIdentityNumber((string) $user->identity_number),
                'identity_country' => $user->identity_country,
                'provider_reference' => $result['reference'],
                'request_payload' => [
                    'endpoint' => $result['endpoint'],
                    'query' => $result['query'],
                ],
                'response_payload' => [
                    'entity' => $result['entity'],
                    'normalized' => $result['normalized'],
                    'checks' => $result['checks'],
                ],
                'verified_at' => now(),
                'notes' => $result['recommended_status'] === 'approved'
                    ? 'Dojah sandbox lookup returned a matching identity profile.'
                    : 'Dojah sandbox lookup completed but requires admin review before final approval.',
            ]);

            $user->forceFill([
                'kyc_status' => $result['recommended_status'],
                'kyc_submitted_at' => $user->kyc_submitted_at ?? now(),
                'kyc_approved_at' => $result['recommended_status'] === 'approved' ? now() : null,
            ])->save();

            return back()->with('success', 'Dojah sandbox verification completed. KYC status updated to ' . str_replace('_', ' ', $result['recommended_status']) . '.');
        } catch (\Throwable $e) {
            $hint = $dojahKycService->sandboxHintFor($user->identity_type);
            $message = $e->getMessage();

            if ($hint && str_contains(strtolower($message), 'wrong nin inputted')) {
                $message .= ' ' . $hint;
            }

            KycVerification::create([
                'user_id' => $user->id,
                'checked_by' => auth()->id(),
                'provider' => 'dojah',
                'environment' => str_contains((string) config('kiosk.kyc.dojah.base_url'), 'sandbox') ? 'sandbox' : 'production',
                'status' => 'failed',
                'identity_type' => $user->identity_type,
                'identity_number_masked' => $this->maskIdentityNumber((string) $user->identity_number),
                'identity_country' => $user->identity_country,
                'notes' => $message,
            ]);

            return back()->with('error', 'Dojah sandbox verification could not be completed: ' . $message);
        }
    }

    private function maskIdentityNumber(string $value): string
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return '';
        }

        if (strlen($trimmed) <= 4) {
            return str_repeat('*', strlen($trimmed));
        }

        return substr($trimmed, 0, 2) . str_repeat('*', max(strlen($trimmed) - 4, 0)) . substr($trimmed, -2);
    }
}

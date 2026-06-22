<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-slate-900">Verify your email</h1>
            <p class="mt-2 text-sm text-slate-600">Please click the verification link we sent to your email to continue.</p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success border-0 radius-10 shadow-sm p-3 text-sm" role="alert" style="background-color: #d1e7dd; color: #0f5132; border-radius: 10px;">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button class="w-full justify-center">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full justify-center text-center text-sm text-slate-600 underline underline-offset-4 hover:text-slate-900 bg-transparent border-0 py-2">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

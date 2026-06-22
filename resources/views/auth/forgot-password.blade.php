<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-slate-900">Reset your password</h1>
            <p class="mt-2 text-sm text-slate-600">Enter the email tied to your Kiosk account and we’ll send you a reset link.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="space-y-3">
                <x-primary-button class="w-full justify-center">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>

                <p class="text-center text-sm text-slate-600">
                    <a href="{{ route('login') }}" class="font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
                        Back to Login
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>

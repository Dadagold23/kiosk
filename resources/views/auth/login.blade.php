<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-slate-900">Sign in to Kiosk</h1>
            <p class="mt-2 text-sm text-slate-600">Access your orders, cart, bookings, and service requests.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <div class="flex items-center justify-between gap-4">
                    <x-input-label for="password" :value="__('Password')" />
                    @if (Route::has('password.request'))
                        <a class="text-sm text-slate-600 underline underline-offset-4 hover:text-slate-900" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <x-text-input id="password" class="mt-1 block w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="show_password_login" class="inline-flex items-center">
                    <input id="show_password_login" type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-500">
                    <span class="ms-2 text-sm text-slate-600">Show password</span>
                </label>
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-500" name="remember">
                    <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="space-y-3">
                <x-primary-button class="w-full justify-center">
                    {{ __('Log in') }}
                </x-primary-button>

                @if (Route::has('register'))
                    <p class="text-center text-sm text-slate-600">
                        Need an account?
                        <a href="{{ route('register') }}" class="font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
                            Register
                        </a>
                    </p>
                @endif
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkbox = document.getElementById('show_password_login');
        var password = document.getElementById('password');

        if (!checkbox || !password) {
            return;
        }

        checkbox.addEventListener('change', function () {
            password.type = checkbox.checked ? 'text' : 'password';
        });
    });
    </script>
    @endpush
</x-guest-layout>

<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-slate-900">Create a new password</h1>
            <p class="mt-2 text-sm text-slate-600">Choose a strong password so you can access Kiosk securely.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('New Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="show_password_reset" class="inline-flex items-center">
                    <input id="show_password_reset" type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-500">
                    <span class="ms-2 text-sm text-slate-600">Show passwords</span>
                </label>
            </div>

            <div class="space-y-3">
                <x-primary-button class="w-full justify-center">
                    {{ __('Reset Password') }}
                </x-primary-button>

                <p class="text-center text-sm text-slate-600">
                    <a href="{{ route('login') }}" class="font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
                        Return to Login
                    </a>
                </p>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkbox = document.getElementById('show_password_reset');
        var password = document.getElementById('password');
        var confirmation = document.getElementById('password_confirmation');

        if (!checkbox || !password || !confirmation) {
            return;
        }

        checkbox.addEventListener('change', function () {
            var type = checkbox.checked ? 'text' : 'password';
            password.type = type;
            confirmation.type = type;
        });
    });
    </script>
    @endpush
</x-guest-layout>

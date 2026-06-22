<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-slate-900">Create your account</h1>
            <p class="mt-2 text-sm text-slate-600">Register once to track products, services, bookings, and consultancy.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="show_password_register" class="inline-flex items-center">
                    <input id="show_password_register" type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-500">
                    <span class="ms-2 text-sm text-slate-600">Show passwords</span>
                </label>
            </div>

            <div class="space-y-3">
                <x-primary-button class="w-full justify-center">
                    {{ __('Register') }}
                </x-primary-button>

                <p class="text-center text-sm text-slate-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
                        Log in
                    </a>
                </p>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkbox = document.getElementById('show_password_register');
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

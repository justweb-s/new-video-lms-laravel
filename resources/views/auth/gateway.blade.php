<x-guest-layout>
    @php
        $tab = request('tab', 'login');
        $isLogin = $tab !== 'register';
    @endphp

    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex border-b border-gray-200">
            <a href="{{ route('login', ['tab' => 'login']) }}"
               class="px-4 py-2 -mb-px border-b-2 text-sm font-medium {{ $isLogin ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Accedi
            </a>
            <a href="{{ route('login', ['tab' => 'register']) }}"
               class="px-4 py-2 -mb-px border-b-2 text-sm font-medium {{ !$isLogin ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Registrati
            </a>
        </div>

        @if ($isLogin)
            <!-- LOGIN TAB -->
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="bg-white p-6 rounded-lg shadow-sm">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Ricordami') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('password.request') }}">
                            {{ __('Hai dimenticato la password?') }}
                        </a>
                    @endif

                    <x-primary-button>
                        {{ __('Accedi') }}
                    </x-primary-button>
                </div>
            </form>
        @else
            <!-- REGISTER TAB -->
            <form method="POST" action="{{ route('register') }}" class="bg-white p-6 rounded-lg shadow-sm">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Conferma Password')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('login', ['tab' => 'login']) }}">
                        {{ __('Sei già registrato?') }}
                    </a>

                    <x-primary-button>
                        {{ __('Registrati') }}
                    </x-primary-button>
                </div>
            </form>
        @endif
    </div>
</x-guest-layout>

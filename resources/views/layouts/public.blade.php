<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white/95 backdrop-blur border-b border-primary/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('catalog.index') }}" class="flex items-center">
                                <x-brand-logo class="block h-8 w-auto" />
                            </a>
                            <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-primary hover:text-primary/80">Catalogo</a>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if(Auth::check())
                                <a href="{{ route('dashboard') }}" class="text-sm text-primary hover:text-primary/80">Dashboard</a>
                                <a href="{{ route('profile.edit') }}" class="text-sm text-primary hover:text-primary/80">Profilo</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-primary hover:text-primary/80">Esci</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-primary hover:text-primary/80">Accedi</a>
                                <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-primary text-white text-sm hover:bg-primary/90">Registrati</a>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                @if (session('status'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                        <div class="rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                        <div class="rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-primary text-white border-b border-primary/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                                <x-brand-logo class="h-8 w-auto" />
                                <span class="text-lg font-semibold">Admin</span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-accent text-white' : 'border-transparent text-white/80 hover:text-white hover:border-accent/80' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.courses.*') ? 'border-accent text-white' : 'border-transparent text-white/80 hover:text-white hover:border-accent/80' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Corsi
                            </a>
                            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.students.*') ? 'border-accent text-white' : 'border-transparent text-white/80 hover:text-white hover:border-accent/80' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Studenti
                            </a>
                            <a href="{{ route('admin.giftcards.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.giftcards.*') ? 'border-accent text-white' : 'border-transparent text-white/80 hover:text-white hover:border-accent/80' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Gift Card
                            </a>
                            <a href="{{ route('admin.workout-cards.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.workout-cards.*') ? 'border-accent text-white' : 'border-transparent text-white/80 hover:text-white hover:border-accent/80' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Schede di Allenamento
                            </a>
                            <a href="{{ route('admin.settings.contact.edit') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.settings.*') ? 'border-accent text-white' : 'border-transparent text-white/80 hover:text-white hover:border-accent/80' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Impostazioni
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button id="mobile-menu-button" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-white/90 hover:text-white hover:bg-primary/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-accent" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Apri menu</span>
                            <!-- Menu open icon -->
                            <svg id="icon-menu" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Menu close icon -->
                            <svg id="icon-close" class="h-6 w-6 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div class="relative">
                                <button type="button" class="flex text-sm bg-primary rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ substr(Auth::guard('admin')->user()->username, 0, 1) }}</span>
                                    </div>
                                </button>
                            </div>
                            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <div class="px-4 py-2 text-xs text-gray-400">
                                    {{ Auth::guard('admin')->user()->full_name }}
                                </div>
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Panel -->
            <div id="mobile-menu" class="sm:hidden hidden border-t border-primary/50">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary/20 border-accent text-white' : 'border-transparent text-white/80 hover:bg-primary/30 hover:border-accent/60 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('admin.courses.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin.courses.*') ? 'bg-primary/20 border-accent text-white' : 'border-transparent text-white/80 hover:bg-primary/30 hover:border-accent/60 hover:text-white' }}">Corsi</a>
                    <a href="{{ route('admin.students.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin.students.*') ? 'bg-primary/20 border-accent text-white' : 'border-transparent text-white/80 hover:bg-primary/30 hover:border-accent/60 hover:text-white' }}">Studenti</a>
                    <a href="{{ route('admin.giftcards.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin.giftcards.*') ? 'bg-primary/20 border-accent text-white' : 'border-transparent text-white/80 hover:bg-primary/30 hover:border-accent/60 hover:text-white' }}">Gift Card</a>
                    <a href="{{ route('admin.workout-cards.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin.workout-cards.*') ? 'bg-primary/20 border-accent text-white' : 'border-transparent text-white/80 hover:bg-primary/30 hover:border-accent/60 hover:text-white' }}">Schede di Allenamento</a>
                    <a href="{{ route('admin.settings.contact.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-primary/20 border-accent text-white' : 'border-transparent text-white/80 hover:bg-primary/30 hover:border-accent/60 hover:text-white' }}">Impostazioni</a>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @if(request()->cookie(config('cookie-consent.cookie_name')) === '1')
        @includeIf('partials.analytics-consent')
    @endif

    <script>
        // Simple dropdown toggle (null-safe and robust)
        (function() {
            const button = document.getElementById('user-menu-button');
            const dropdown = document.querySelector('[role="menu"][aria-labelledby="user-menu-button"]');

            if (!button || !dropdown) {
                return;
            }

            button.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        })();
    </script>
    <script>
        (function() {
            const mobileBtn = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const iconMenu = document.getElementById('icon-menu');
            const iconClose = document.getElementById('icon-close');
            if (!mobileBtn || !mobileMenu || !iconMenu || !iconClose) { return; }
            mobileBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                mobileMenu.classList.toggle('hidden');
                iconMenu.classList.toggle('hidden');
                iconClose.classList.toggle('hidden');
            });
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileBtn.contains(event.target)) {
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                        iconMenu.classList.remove('hidden');
                        iconClose.classList.add('hidden');
                    }
                }
            });
        })();
    </script>
</body>
</html>

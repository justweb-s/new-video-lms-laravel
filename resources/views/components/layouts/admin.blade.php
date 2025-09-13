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

    {{-- Trix Editor --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>

    <style>
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none;
        }
    </style>

</head>
<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar" :class="{'mobile-open': sidebarOpen}">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-white no-underline">
                    <x-brand-logo class="h-8 w-auto" />
                    <h2 class="text-lg font-semibold">Admin</h2>
                </a>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            Dashboard
                        </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.courses.index') }}" class="menu-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Corsi
                        </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.students.index') }}" class="menu-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            Studenti
                        </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.payments.index') }}" class="menu-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zM14 6a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM6 8a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 012-2h2z"/>
                            </svg>
                            Ordini
                        </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.workout-cards.index') }}" class="menu-link {{ request()->routeIs('admin.workout-cards.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9zm2 0a1 1 0 100 2h.01a1 1 0 100-2H11zm2 0a1 1 0 100 2h.01a1 1 0 100-2H13zm-4-2a1 1 0 100 2h.01a1 1 0 100-2H9zm2 0a1 1 0 100 2h.01a1 1 0 100-2H11zm2 0a1 1 0 100 2h.01a1 1 0 100-2H13z" clip-rule="evenodd"/>
                            </svg>
                            Schede Allenamento
                        </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.giftcards.index') }}" class="menu-link {{ request()->routeIs('admin.giftcards.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                            </svg>
                            Gift Card
                        </span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.blog-posts.index') }}" class="menu-link {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                                <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>
                            </svg>
                            Blog
                        </span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.media.index') }}" class="menu-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v6l-3-2-3 4-2-3-4 5V5z"/>
                            </svg>
                            Galleria Media
                        </span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.data.index') }}" class="menu-link {{ request()->routeIs('admin.data.*') ? 'active' : '' }}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 011 1v3H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h7a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zm10-5a1 1 0 011-1h3a1 1 0 011 1v9a2 2 0 01-2 2h-3a1 1 0 01-1-1V5z" clip-rule="evenodd"/>
                            </svg>
                            Import/Export
                        </span>
                    </a>
                </li>

                <!-- Dropdown Item -->
                <li class="menu-item" x-data="{ open: @js(request()->routeIs('admin.settings.*')) }">
                    <div @click="open = !open" class="menu-link" :class="{'active': open}">
                        <span class="menu-text">
                            <svg class="menu-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                            Impostazioni
                        </span>
                        <span class="dropdown-arrow" :class="{'rotated': open}">▼</span>
                    </div>
                    <div class="submenu" x-show="open" :class="{ 'open': open }">
                        <a href="{{ route('admin.settings.seo.edit') }}" class="submenu-item {{ request()->routeIs('admin.settings.seo.*') ? 'active' : '' }}">SEO</a>
                        <a href="{{ route('admin.settings.contact.edit') }}" class="submenu-item {{ request()->routeIs('admin.settings.contact.*') ? 'active' : '' }}">Contatti</a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <button class="mobile-toggle" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex items-center ml-auto">
                    <!-- Settings Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                            <div>{{ Auth::guard('admin')->user()->full_name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-75" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0" style="display: none;">
                            <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

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

            <!-- Overlay for mobile -->
            <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-900 md:hidden" @click="sidebarOpen = false"></div>
        </div>
    </div>

    @php
        $cc = request()->cookie(config('cookie-consent.cookie_name'));
        $analyticsAllowed = $cc === '1';
        if (!$analyticsAllowed && is_string($cc) && str_starts_with($cc, '{')) {
            try {
                $ccArr = json_decode($cc, true);
                $analyticsAllowed = is_array($ccArr) && !empty($ccArr['analytics']);
            } catch (\Throwable $e) {
                $analyticsAllowed = false;
            }
        }
    @endphp
    @if($analyticsAllowed)
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

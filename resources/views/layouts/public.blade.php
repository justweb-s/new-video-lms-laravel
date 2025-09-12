<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('partials.seo')

        <link rel="icon" href="{{ asset('images/favicon-studio.png') }}" type="image/png">
        <link rel="shortcut icon" href="{{ asset('images/favicon-studio.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important}</style>
        @stack('styles')
        <style id="cookie-consent-styles">
            .cookie-consent {
                position: fixed; bottom: 20px; left: 20px; right: 20px; z-index: 5000;
                background: #36583c; color: #ffffff; border: 2px solid #f4e648;
                padding: 16px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,.2);
                display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
            }
            .cookie-consent__message { font-size: 0.95rem; line-height: 1.4; color: #ffffff; }
            .cookie-consent__message a { color: #f4e648; text-decoration: underline; font-weight: 600; }
            .cookie-consent__agree {
                background: #f4e648; color: #111; font-weight: 700; cursor: pointer;
                padding: 10px 16px; border-radius: 10px; border: 2px solid #111; transition: transform .15s ease, box-shadow .15s ease;
            }
            .cookie-consent__agree:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.15); }
            .cookie-consent__deny {
                background: transparent; color: #ffffff; font-weight: 700;
                padding: 10px 16px; border-radius: 10px; border: 2px solid #ffffff; transition: transform .15s ease, box-shadow .15s ease;
            }
            .cookie-consent__preferences {
                background: transparent; color: #f4e648; font-weight: 700;
                padding: 10px 16px; border-radius: 10px; border: 2px dashed #f4e648; transition: transform .15s ease, box-shadow .15s ease;
            }
            .cookie-consent__actions { display:flex; align-items:center; gap:12px; margin-left:auto; flex-wrap:wrap; }
            /* Modal */
            .cc-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 6000; }
            .cc-modal { position: fixed; z-index: 7000; inset: 0; align-items:center; justify-content:center; }
            .cc-modal .cc-card { background: #ffffff; color:#111; width: 96%; max-width: 680px; border-radius: 14px; overflow: hidden; border: 2px solid #36583c; }
            .cc-modal .cc-card .cc-header { background: #36583c; color: #fff; padding: 16px 20px; font-weight: 700; display:flex; align-items:center; justify-content:space-between; }
            .cc-modal .cc-card .cc-body { padding: 16px 20px; }
            .cc-modal .cc-card .cc-actions { display:flex; gap:12px; justify-content:flex-end; padding: 16px 20px; border-top:1px solid #e5e7eb; }
            .cc-btn { padding: 10px 16px; border-radius: 10px; font-weight: 700; }
            .cc-btn-primary { background:#36583c; color:#fff; border:2px solid #111; }
            .cc-btn-secondary { background:#f4e648; color:#111; border:2px solid #111; }
        </style>
    </head>
    <body x-data="{ openCart: false }" x-on:keydown.escape.window="openCart=false" x-on:open-cart.window="openCart=true" class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white/95 backdrop-blur border-b border-primary/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('static.home') }}" class="flex items-center">
                                <x-brand-logo class="block h-8 w-auto" />
                            </a>
                            <div class="hidden sm:flex items-center space-x-6">
                                <a href="{{ route('static.home') }}" class="text-sm font-medium text-primary hover:text-primary/80">Home</a>
                                <a href="{{ route('static.about') }}" class="text-sm font-medium text-primary hover:text-primary/80">Chi Sono</a>
                                <a href="{{ route('static.workout-online') }}" class="text-sm font-medium text-primary hover:text-primary/80">Workout Online</a>
                                <a href="{{ route('static.workout-in-studio') }}" class="text-sm font-medium text-primary hover:text-primary/80">Workout in Studio</a>
                                <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-primary hover:text-primary/80">Catalogo</a>
                                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-primary hover:text-primary/80">Blog</a>
                                <a href="{{ route('giftcards.index') }}" class="text-sm font-medium text-primary hover:text-primary/80">Gift Card</a>
                                <a href="{{ route('static.contact') }}" class="text-sm font-medium text-primary hover:text-primary/80">Contatti</a>
                                <a href="{{ route('static.book-a-consultation') }}" class="text-sm font-medium text-primary hover:text-primary/80">Prenota una consulenza</a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @php $cartCount = is_array(session('cart.items')) ? count(session('cart.items')) : 0; @endphp
                            <button type="button" @click="openCart = true" class="relative inline-flex items-center justify-center p-2 rounded-full hover:bg-primary/10 text-primary focus:outline-none focus:ring-2 focus:ring-primary/30" aria-label="Apri carrello" title="Carrello">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-6-4a2 2 0 100 4 2 2 0 000-4zM9 18a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                @if($cartCount > 0)
                                    <span class="absolute top-0 right-0 translate-x-1/3 -translate-y-1/3 w-4 h-4 inline-flex items-center justify-center rounded-full bg-primary text-white text-[10px] leading-none ring-2 ring-white shadow">{{ $cartCount }}</span>
                                @endif
                            </button>
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
                            <!-- Mobile menu button -->
                            <button id="mobile-menu-button-public" type="button" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-primary hover:text-primary/80 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary/30" aria-controls="mobile-menu-public" aria-expanded="false">
                                <span class="sr-only">Apri menu</span>
                                <!-- Menu open icon -->
                                <svg id="icon-menu-public" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <!-- Menu close icon -->
                                <svg id="icon-close-public" class="h-6 w-6 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Mobile Menu Panel -->
                <div id="mobile-menu-public" class="sm:hidden hidden border-t border-primary/10">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('static.home') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('static.home') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Home</a>
                        <a href="{{ route('static.about') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('static.about') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Chi Sono</a>
                        <a href="{{ route('static.workout-online') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('static.workout-online') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Workout Online</a>
                        <a href="{{ route('static.workout-in-studio') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('static.workout-in-studio') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Workout in Studio</a>
                        <a href="{{ route('catalog.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('catalog.*') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Catalogo</a>
                        <a href="{{ route('blog.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('blog.*') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Blog</a>
                        <a href="{{ route('giftcards.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('giftcards.*') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Gift Card</a>
                        <a href="{{ route('static.contact') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('static.contact') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Contatti</a>
                        <a href="{{ route('static.book-a-consultation') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('static.book-a-consultation') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Prenota una consulenza</a>
                        @if(Auth::check())
                            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Dashboard</a>
                            <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('profile.edit') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Profilo</a>
                            <form method="POST" action="{{ route('logout') }}" class="pl-3 pr-4 py-2">
                                @csrf
                                <button type="submit" class="text-left w-full text-base font-medium text-gray-600 hover:text-gray-800">Esci</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('login') ? 'bg-gray-50 border-primary text-gray-900' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }}">Accedi</a>
                            <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium text-white bg-primary/90 rounded-md mx-3 text-center">Registrati</a>
                        @endif
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
            
            <!-- Footer -->
            <footer class="bg-gradient-to-r from-yellow-400 to-yellow-300" style="background: linear-gradient(135deg, #f6e849 0%, #f4e030 100%);">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Logo e Descrizione -->
                        <div class="md:col-span-1">
                            <a href="{{ route('static.home') }}" class="flex items-center mb-4">
                                <x-brand-logo class="block h-10 w-auto" />
                            </a>
                            <div class="mt-6">
                                <h4 class="font-bold text-green-800 text-sm mb-3" style="font-family: 'Montserrat', sans-serif; text-transform: uppercase;">SEGUIMI SUI SOCIAL</h4>
                                <a href="#" class="text-green-800 hover:text-green-600 transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.097.118.112.221.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.749-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24c6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001.017 0z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Programmi -->
                        <div class="md:col-span-1">
                            <h4 class="font-bold text-green-800 text-lg mb-4" style="font-family: 'Montserrat', sans-serif; text-transform: uppercase;">PROGRAMMI</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('static.contact') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Workout in Studio</a></li>
                                <li><a href="{{ route('static.workout-online') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Workout Online</a></li>
                                <li><a href="{{ route('static.workout-online') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Burn Fit</a></li>
                                <li><a href="{{ route('static.workout-online') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Booty Boost</a></li>
                                <li><a href="{{ route('static.workout-online') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Sculpt Fit</a></li>
                                <li><a href="{{ route('static.contact') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Workout Personalizzato Online</a></li>
                            </ul>
                        </div>
                        
                        <!-- Informazioni -->
                        <div class="md:col-span-1">
                            <h4 class="font-bold text-green-800 text-lg mb-4" style="font-family: 'Montserrat', sans-serif; text-transform: uppercase;">INFORMAZIONI</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('privacy-policy') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Privacy Policy</a></li>
                                <li><a href="{{ route('privacy-policy') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Termini e Condizioni</a></li>
                                <li><a href="{{ route('static.contact') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Contatti</a></li>
                                <li><a href="{{ route('cookie-policy') }}" class="text-green-700 hover:text-green-600 transition-colors text-sm" style="font-family: 'Source Sans Pro', sans-serif;">Cookie Policy (UE)</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Copyright Bar -->
                <div class="bg-green-800 py-4">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center">
                            <p class="text-yellow-400 text-sm" style="font-family: 'Source Sans Pro', sans-serif;">
                                Copyright © 2025 Emy Workout | Powered by JustWebsite
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        @guest
            @include('cookie-consent::index')
        @endguest

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

        @include('partials.cart-drawer')

        <script>
            (function() {
                const btn = document.getElementById('mobile-menu-button-public');
                const menu = document.getElementById('mobile-menu-public');
                const iconMenu = document.getElementById('icon-menu-public');
                const iconClose = document.getElementById('icon-close-public');
                if (!btn || !menu || !iconMenu || !iconClose) { return; }
                btn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    menu.classList.toggle('hidden');
                    iconMenu.classList.toggle('hidden');
                    iconClose.classList.toggle('hidden');
                });
                document.addEventListener('click', function(event) {
                    if (!menu.contains(event.target) && !btn.contains(event.target)) {
                        if (!menu.classList.contains('hidden')) {
                            menu.classList.add('hidden');
                            iconMenu.classList.remove('hidden');
                            iconClose.classList.add('hidden');
                        }
                    }
                });
            })();
        </script>

        @stack('scripts')
    </body>
</html>

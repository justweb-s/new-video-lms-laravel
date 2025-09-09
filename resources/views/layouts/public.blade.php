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
            /* Modal */
            .cc-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 6000; display:none; }
            .cc-modal { position: fixed; z-index: 7000; inset: 0; display:none; align-items:center; justify-content:center; }
            .cc-modal .cc-card { background: #ffffff; color:#111; width: 96%; max-width: 680px; border-radius: 14px; overflow: hidden; border: 2px solid #36583c; }
            .cc-modal .cc-card .cc-header { background: #36583c; color: #fff; padding: 16px 20px; font-weight: 700; display:flex; align-items:center; justify-content:space-between; }
            .cc-modal .cc-card .cc-body { padding: 16px 20px; }
            .cc-modal .cc-card .cc-actions { display:flex; gap:12px; justify-content:flex-end; padding: 16px 20px; border-top:1px solid #e5e7eb; }
            .cc-btn { padding: 10px 16px; border-radius: 10px; font-weight: 700; }
            .cc-btn-primary { background:#36583c; color:#fff; border:2px solid #111; }
            .cc-btn-secondary { background:#f4e648; color:#111; border:2px solid #111; }
        </style>
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
                            <a href="{{ route('giftcards.index') }}" class="text-sm font-medium text-primary hover:text-primary/80">Gift Card</a>
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

        @guest
            @include('cookie-consent::index')
        @endguest

        @if(request()->cookie(config('cookie-consent.cookie_name')) === '1')
            @includeIf('partials.analytics-consent')
        @endif

        @stack('scripts')
    </body>
</html>

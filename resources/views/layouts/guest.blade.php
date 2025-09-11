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
        <style>[x-cloak]{display:none!important}</style>
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
            .cookie-consent__deny {
                background: transparent; color: #ffffff; font-weight: 700;
                padding: 10px 16px; border-radius: 10px; border: 2px solid #ffffff; transition: transform .15s ease, box-shadow .15s ease;
            }
            .cookie-consent__preferences {
                background: transparent; color: #f4e648; font-weight: 700;
                padding: 10px 16px; border-radius: 10px; border: 2px dashed #f4e648; transition: transform .15s ease, box-shadow .15s ease;
            }
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
    <body x-data="{ openCart: false }" x-on:keydown.escape.window="openCart=false" class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <img src="{{ asset('images/favicon-studio.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="w-48">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        @include('cookie-consent::index')

        @php $cartCount = is_array(session('cart.items')) ? count(session('cart.items')) : 0; @endphp
        <button type="button" @click="openCart = true" class="fixed bottom-5 right-5 z-[9997] relative inline-flex items-center justify-center p-3 rounded-full shadow-lg bg-white border border-primary/20 text-primary hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary/30" aria-label="Apri carrello" title="Carrello">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-6-4a2 2 0 100 4 2 2 0 000-4zM9 18a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            @if($cartCount > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-primary text-white text-[10px] leading-none px-1.5 py-0.5">{{ $cartCount }}</span>
            @endif
        </button>

        @if(request()->cookie(config('cookie-consent.cookie_name')) === '1')
            @includeIf('partials.analytics-consent')
        @endif

        @include('partials.cart-drawer')
    </body>
</html>

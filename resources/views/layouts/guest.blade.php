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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        @include('cookie-consent::index')

        @if(request()->cookie(config('cookie-consent.cookie_name')) === '1')
            @includeIf('partials.analytics-consent')
        @endif
    </body>
</html>

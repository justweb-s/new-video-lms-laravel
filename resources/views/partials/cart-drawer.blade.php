@php
    $items = is_array(session('cart.items')) ? session('cart.items') : [];
    $count = count($items);
    $total = 0;
    foreach ($items as $it) { $total += (float) ($it['price'] ?? 0); }
@endphp

<!-- Cart Drawer and Overlay -->
<div x-cloak x-show="openCart" x-transition.opacity class="fixed inset-0 bg-black/35 z-[9998]" @click="openCart = false"></div>

<aside x-cloak x-show="openCart"
       x-transition:enter="transition ease-in-out duration-300"
       x-transition:enter-start="translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       class="fixed right-0 top-0 h-full w-[340px] sm:w-[400px] bg-white shadow-2xl z-[9999] flex flex-col">
    <header class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-6-4a2 2 0 100 4 2 2 0 000-4zM9 18a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2 class="text-lg font-semibold text-gray-900">Il tuo carrello</h2>
            <span class="ml-2 inline-flex items-center justify-center rounded-full bg-primary text-white text-xs px-2 py-0.5">{{ $count }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($count > 0)
                <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Svuotare il carrello?')">
                    @csrf
                    <button class="text-xs text-gray-500 hover:text-gray-700">Svuota</button>
                </form>
            @endif
            <button type="button" @click="openCart = false" class="p-2 rounded hover:bg-gray-50" aria-label="Chiudi carrello">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto">
        <div class="p-6 text-gray-500" @class(['hidden' => $count>0]) data-cart-empty>Il carrello è vuoto.</div>
        <ul id="cart-drawer-items" role="list" class="divide-y divide-gray-100">
            @foreach($items as $it)
                @php
                    $image = $it['image'] ?? null;
                    $imageUrl = $image ? (\Illuminate\Support\Str::startsWith($image, ['http://','https://']) ? $image : \Illuminate\Support\Facades\Storage::url($image)) : null;
                @endphp
                <li class="p-4 flex items-start gap-3">
                    <div class="w-12 h-12 rounded border border-gray-200 bg-gray-100 overflow-hidden flex-shrink-0">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $it['name'] ?? 'item' }}" class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $it['name'] ?? '—' }}</p>
                            <p class="text-sm font-semibold text-gray-900">€{{ number_format((float)($it['price'] ?? 0), 2, ',', '.') }}</p>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 capitalize">{{ $it['type'] ?? '' }}</p>
                        @if(($it['type'] ?? '') === 'gift_card')
                            <p class="mt-1 text-xs text-gray-500">Per: {{ $it['gift']['recipient_name'] ?? '' }} ({{ $it['gift']['recipient_email'] ?? '' }})</p>
                            @if(!empty($it['gift']['message']))
                                <p class="mt-1 text-xs text-gray-400">"{{ $it['gift']['message'] }}"</p>
                            @endif
                        @endif
                    </div>
                    <div>
                        <form method="POST" action="{{ route('cart.remove', $it['id']) }}" onsubmit="return confirm('Rimuovere questo elemento?')">
                            @csrf
                            @method('DELETE')
                            <button class="p-2 rounded border border-gray-200 text-gray-500 hover:bg-gray-50" aria-label="Rimuovi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <footer class="p-4 sm:p-6 border-t border-gray-100">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">Totale</div>
            <div id="cart-drawer-total" class="text-xl font-semibold text-gray-900">€{{ number_format((float)$total, 2, ',', '.') }}</div>
        </div>
        <div class="mt-4 flex gap-3">
            <a href="{{ route('cart.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Vedi carrello</a>
            <a href="{{ route('cart.checkout') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Checkout</a>
        </div>
    </footer>
</aside>

<script>
    document.addEventListener('alpine:init', () => {
        // Chiudi con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                try { if (window.Alpine && Alpine.store && Alpine.store('openCart') !== undefined) {} } catch (err) {}
            }
        });
    });
</script>

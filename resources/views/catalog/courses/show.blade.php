@extends('layouts.public')

@section('title', $course->name)
@section('meta_description', Str::limit(strip_tags($course->description), 155))
@section('meta_image', $imageSrc ?? asset('images/favicon-studio.png'))


@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            @php
                $image = $course->image_url;
                $imageSrc = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : Storage::url($image)) : null;
            @endphp
            @if($imageSrc)
                <img src="{{ $imageSrc }}" alt="{{ $course->name }}" class="w-full rounded-lg shadow-sm object-cover">
            @else
                <div class="w-full h-72 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $course->name }}</h1>
            <div class="mt-4 prose max-w-none">
                <p class="text-gray-700">{{ $course->description }}</p>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <dt class="text-sm text-gray-500">Prezzo</dt>
                    <dd class="mt-1 text-2xl font-semibold text-primary">€{{ number_format($course->price, 2, ',', '.') }}</dd>
                </div>
                @if(!empty($course->duration_days))
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <dt class="text-sm text-gray-500">Durata accesso</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $course->duration_days }} giorni</dd>
                </div>
                @endif
                @if(!empty($course->prerequisites))
                <div class="bg-white rounded-lg p-4 shadow-sm sm:col-span-2">
                    <dt class="text-sm text-gray-500">Prerequisiti</dt>
                    <dd class="mt-1 text-gray-900">{{ $course->prerequisites }}</dd>
                </div>
                @endif
            </dl>

            <div class="mt-8">
                @if($isEnrolled)
                    <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-6 py-3 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Vai al corso</a>
                @else
                    <a href="{{ route('catalog.checkout', $course) }}" class="inline-flex items-center px-6 py-3 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Acquista ora</a>
                    <a href="{{ route('catalog.checkout', [$course, 'provider' => 'paypal']) }}" class="inline-flex items-center px-6 py-3 rounded-md bg-[#ffc439] text-gray-900 font-semibold hover:brightness-95 ml-3">Acquista con PayPal</a>
                    <form method="POST" action="{{ route('cart.add-course', $course) }}" class="inline" data-add-to-cart>
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-3 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 ml-3">Aggiungi al carrello</button>
                    </form>
                    <a href="{{ route('giftcards.show', $course) }}" class="inline-flex items-center px-6 py-3 rounded-md border border-primary text-primary font-semibold hover:bg-primary/5 ml-3">Regala questo corso</a>
                    <p class="mt-3 text-sm text-gray-500">Verrai reindirizzato all'accesso se non sei autenticato.</p>

                    <div class="mt-6 bg-white rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-700">Hai un codice gift card?</p>
                        <form method="GET" action="{{ route('catalog.checkout', $course) }}" class="mt-2 flex items-center gap-2">
                            <input type="text" name="gift_code" value="{{ request('gift_code') }}" placeholder="Inserisci codice" class="border rounded px-3 py-2 text-sm w-56" />
                            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded">Usa gift code</button>
                        </form>
                        <p class="mt-2 text-xs text-gray-500">Se non sei autenticato, ti verrà richiesto di accedere.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[data-add-to-cart]');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const url = form.getAttribute('action');
    const token = form.querySelector('input[name="_token"]').value;
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
      });
      if (!res.ok) throw new Error('Request failed');
      const data = await res.json();
      if (!data.ok) throw new Error('Server error');

      // Aggiorna/crea badge nell'header
      const updateBadge = (container) => {
        if (!container) return;
        let badge = container.querySelector('span');
        if (!badge) {
          badge = document.createElement('span');
          badge.className = 'absolute top-0 right-0 translate-x-1/3 -translate-y-1/3 w-4 h-4 inline-flex items-center justify-center rounded-full bg-primary text-white text-[10px] leading-none ring-2 ring-white shadow';
          container.appendChild(badge);
        }
        badge.textContent = data.count;
      };
      document.querySelectorAll('button[title="Carrello"]').forEach(updateBadge);
      document.querySelectorAll('a[title="Carrello"]').forEach(updateBadge);

      // Aggiorna drawer: ricarica stato minimale e apri
      try {
        const stateRes = await fetch('{{ route('cart.state') }}', { headers: { 'Accept': 'application/json' } });
        const state = await stateRes.json();
        if (state && state.ok) {
          const list = document.getElementById('cart-drawer-items');
          const emptyMsg = document.querySelector('[data-cart-empty]');
          const totalEl = document.getElementById('cart-drawer-total');
          if (list) {
            list.innerHTML = '';
            state.items.forEach((it) => {
              const li = document.createElement('li');
              li.className = 'p-4 flex items-start gap-3';
              li.innerHTML = `
                <div class="w-12 h-12 rounded border border-gray-200 bg-gray-100 overflow-hidden flex-shrink-0">
                  ${it.image ? `<img src="${it.image}" alt="${it.name}" class="w-full h-full object-cover" />` : `<div class=\"w-full h-full flex items-center justify-center text-gray-400\"><svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1.5\" d=\"M3 7h18M3 12h18M3 17h18\"/></svg></div>`}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 truncate">${it.name}</p>
                    <p class="text-sm font-semibold text-gray-900">€${(it.price).toFixed(2).replace('.', ',')}</p>
                  </div>
                  <p class="mt-1 text-xs text-gray-500 capitalize">${it.type}</p>
                </div>
              `;
              list.appendChild(li);
            });
          }
          if (emptyMsg) emptyMsg.classList.add('hidden');
          if (totalEl) totalEl.textContent = '€' + (parseFloat(state.total).toFixed(2).replace('.', ','));
        }
      } catch (err) {}

      // Apri drawer
      if (window.Alpine && document.body.__x) {
        document.body.__x.$data.openCart = true;
      } else {
        document.body.setAttribute('x-data', '{ openCart: true }');
      }
    } catch (err) {
      // fallback: submit normale
      form.submit();
    }
  });
});
</script>
@endpush

@extends('layouts.public')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            @if($course->image_url)
                <img src="{{ asset('storage/' . $course->image_url) }}" alt="{{ $course->name }}" class="w-full rounded-lg shadow-sm object-cover">
            @else
                <div class="w-full h-72 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Regala: {{ $course->name }}</h1>
            <p class="mt-3 text-gray-600">Invia una gift card via email per questo corso. Il destinatario riceverà un codice da riscattare.</p>

            <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <dt class="text-sm text-gray-500">Prezzo</dt>
                    <dd class="mt-1 text-2xl font-semibold text-primary">€{{ number_format($course->price, 2, ',', '.') }}</dd>
                </div>
            </dl>

            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Dati del destinatario</h2>
                <form method="POST" action="{{ route('giftcards.checkout', $course) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nome del destinatario</label>
                        <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                        @error('recipient_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_email" class="block text-sm font-medium text-gray-700">Email del destinatario</label>
                        <input type="email" id="recipient_email" name="recipient_email" value="{{ old('recipient_email') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                        @error('recipient_email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Messaggio (opzionale)</label>
                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" placeholder="Scrivi un messaggio per il destinatario...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pt-2 flex items-center gap-3 flex-wrap">
                        <button type="submit" class="inline-flex items-center px-6 py-3 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Acquista gift card</button>
                        <button type="submit" formaction="{{ route('cart.add-gift-card', $course) }}" class="inline-flex items-center px-6 py-3 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Aggiungi al carrello</button>
                        <p class="mt-2 text-xs text-gray-500 w-full">Ti verrà richiesto di accedere se non sei autenticato.</p>
                    </div>
                </form>
            </div>

            <div class="mt-6 text-sm text-gray-600">
                <p>Hai un codice? <a href="{{ route('giftcards.redeem') }}" class="text-primary hover:underline">Riscatta gift card</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

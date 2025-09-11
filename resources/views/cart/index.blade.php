@extends('layouts.public')

@section('robots', 'noindex, nofollow')


@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-gray-900">Carrello</h1>

    @if(session('status'))
        <div class="mt-4 p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mt-4 p-4 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow-sm">
        <div class="p-4 border-b border-gray-100 flex font-semibold text-gray-700">
            <div class="w-2/5">Articolo</div>
            <div class="w-1/5">Tipo</div>
            <div class="w-1/5 text-right">Prezzo</div>
            <div class="w-1/5 text-right">Azioni</div>
        </div>
        @if(empty($items))
            <div class="p-6 text-gray-500">Il carrello è vuoto.</div>
        @else
            @foreach($items as $it)
                <div class="p-4 border-b border-gray-50 flex items-center {{ !empty($it['invalid']) ? 'opacity-50' : '' }}">
                    <div class="w-2/5">
                        <div class="font-medium text-gray-900">{{ $it['name'] ?? '—' }}</div>
                        @if(($it['type'] ?? '') === 'gift_card')
                            <div class="text-sm text-gray-500">Per: {{ $it['gift']['recipient_name'] ?? '' }} ({{ $it['gift']['recipient_email'] ?? '' }})</div>
                        @endif
                    </div>
                    <div class="w-1/5 capitalize">{{ $it['type'] ?? '' }}</div>
                    <div class="w-1/5 text-right">€{{ number_format((float)($it['price'] ?? 0), 2, ',', '.') }}</div>
                    <div class="w-1/5 text-right">
                        <form method="POST" action="{{ route('cart.remove', $it['id']) }}" onsubmit="return confirm('Rimuovere questo elemento?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-2 text-sm rounded border border-gray-300 hover:bg-gray-50">Rimuovi</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Svuota carrello</button>
        </form>
        <div class="text-right">
            <div class="text-gray-600">Totale</div>
            <div class="text-2xl font-semibold text-primary">€{{ number_format((float)$total, 2, ',', '.') }}</div>
            <a href="{{ route('cart.checkout') }}" class="inline-flex items-center mt-3 px-6 py-3 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Procedi al pagamento</a>
        </div>
    </div>

    <div class="mt-10">
        <a href="{{ route('catalog.index') }}" class="text-primary hover:underline">Continua ad acquistare</a>
    </div>
</div>
@endsection

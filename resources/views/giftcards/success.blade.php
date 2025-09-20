@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900">Acquisto completato</h1>
        </div>
        <p class="mt-2 text-gray-600">La tua gift card è stata emessa con successo. Abbiamo inviato un'email al destinatario con il codice e le istruzioni per il riscatto.</p>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border rounded-lg p-4">
                <h2 class="text-sm text-gray-500">Corso</h2>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $course->name }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <h2 class="text-sm text-gray-500">Valore</h2>
                <p class="mt-1 text-lg font-semibold text-gray-900">€ {{ number_format($gift->amount / 100, 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-6 border rounded-lg p-4 bg-gray-50">
            <h2 class="text-sm text-gray-500">Codice Gift Card</h2>
            <p class="mt-1 text-2xl font-mono tracking-wider">{{ $gift->code }}</p>
            <div class="mt-4 flex items-center gap-3 flex-wrap">
                <a href="{{ route('giftcards.redeem', ['code' => $gift->code]) }}" class="inline-flex items-center px-5 py-2.5 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Riscatta (per il destinatario)</a>
                <a href="mailto:{{ $gift->recipient_email }}" class="inline-flex items-center px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Contatta destinatario</a>
            </div>
            <p class="mt-2 text-xs text-gray-500">Puoi condividere questo link di riscatto: <span class="break-all">{{ route('giftcards.redeem', ['code' => $gift->code]) }}</span></p>
        </div>

        <div class="mt-8 flex items-center gap-3 flex-wrap">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2.5 rounded-md bg-green-600 text-white font-semibold hover:bg-green-700">Vai alla Dashboard</a>
            <a href="{{ route('giftcards.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Acquista un'altra gift card</a>
            <a href="{{ route('giftcards.show', $course) }}" class="inline-flex items-center px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Torna alla pagina del corso</a>
        </div>

        <div class="mt-6 text-sm text-gray-600">
            <p><strong>Destinatario:</strong> {{ $gift->recipient_name }} &lt;{{ $gift->recipient_email }}&gt;</p>
            @if(!empty($gift->message))
                <div class="mt-2">
                    <p class="text-xs text-gray-500">Messaggio incluso:</p>
                    <div class="mt-1 p-3 rounded bg-white border">{{ $gift->message }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

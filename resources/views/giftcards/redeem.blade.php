@extends('layouts.public')

@section('robots', 'noindex, nofollow')


@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900">Riscatta Gift Card</h1>
        <p class="mt-2 text-gray-600">Inserisci il codice ricevuto via email per attivare l'accesso al corso.</p>

        <form method="POST" action="{{ route('giftcards.redeem.submit') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Codice gift card</label>
                <input type="text" id="code" name="code" value="{{ old('code', $code) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" placeholder="Es. GC-ABCD-1234">
                @error('code')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($gift)
                <div class="p-4 bg-gray-50 rounded border">
                    <h2 class="text-lg font-semibold text-gray-900">Dettagli gift card</h2>
                    <ul class="mt-2 text-sm text-gray-700 space-y-1">
                        <li><span class="text-gray-500">Corso:</span> {{ $gift->course->name ?? 'N/D' }}</li>
                        <li><span class="text-gray-500">Destinatario:</span> {{ $gift->recipient_name }} ({{ $gift->recipient_email }})</li>
                        <li><span class="text-gray-500">Importo:</span> {{ number_format($gift->amount/100, 2, ',', '.') }} {{ strtoupper($gift->currency) }}</li>
                        <li><span class="text-gray-500">Stato:</span> {{ strtoupper($gift->status) }}</li>
                    </ul>
                </div>
            @endif

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center px-6 py-3 rounded-md bg-primary text-white font-semibold hover:bg-primary/90">Riscatta codice</button>
            </div>
        </form>

        <div class="mt-6 text-sm text-gray-600">
            <a href="{{ route('giftcards.index') }}" class="text-primary hover:underline">Acquista una gift card</a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.public')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Acquista una Gift Card</h1>
            <p class="mt-2 text-gray-600">Regala l'accesso a un corso. Scegli il corso, inserisci i dati del destinatario e completa il pagamento.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    @if($course->image_url)
                        <img src="{{ asset('storage/' . $course->image_url) }}" alt="{{ $course->name }}" class="w-full h-40 object-cover">
                    @endif
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $course->name }}</h3>
                        <p class="mt-1 text-sm text-gray-600 line-clamp-3">{{ $course->description }}</p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-primary font-bold">{{ number_format($course->price, 2, ',', '.') }} {{ strtoupper(config('services.stripe.currency', 'eur')) }}</span>
                            <a href="{{ route('giftcards.show', $course) }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-primary text-white text-sm hover:bg-primary/90">Regala</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3">
                    <div class="bg-white p-6 rounded shadow-sm text-center text-gray-600">Nessun corso disponibile per l'acquisto.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

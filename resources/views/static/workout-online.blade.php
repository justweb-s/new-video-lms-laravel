@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <div class="prose max-w-none">
            <h1 style="color:#36583c">Workout Online</h1>
            <p>I nostri corsi di allenamento disponibili:</p>
            
            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    @foreach($courses as $course)
                        <div class="bg-gray-50 rounded-lg p-4 border">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($course->description, 100) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 font-bold">€{{ number_format($course->price, 2) }}</span>
                                <a href="{{ route('catalog.show', $course) }}" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                    Visualizza
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600">Nessun corso disponibile al momento.</p>
            @endif
        </div>
    </div>
</div>
@endsection

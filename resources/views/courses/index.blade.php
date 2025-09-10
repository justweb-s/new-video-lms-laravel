@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tutti i corsi</h1>
    </div>

    @if($courses->count() === 0)
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-600">Al momento non ci sono corsi disponibili.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    @php
                        $image = $course->image_url;
                        $imageSrc = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : Storage::url($image)) : null;
                    @endphp
                    @if($imageSrc)
                        <img src="{{ $imageSrc }}" alt="{{ $course->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $course->name }}</h3>
                        <p class="mt-2 text-gray-600">{{ Str::limit($course->description, 120) }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-lg font-semibold text-primary">€{{ number_format($course->price, 2, ',', '.') }}</span>
                            <a href="{{ route('courses.show', $course) }}" class="text-primary hover:text-primary/80 font-medium">Scopri di più →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection

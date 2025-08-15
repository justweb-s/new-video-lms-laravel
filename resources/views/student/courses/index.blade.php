@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">I Miei Corsi</h1>
    
    @if($enrolledCourses->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <p class="text-gray-600 mb-4">Non sei iscritto a nessun corso al momento.</p>
            <a href="{{ route('catalog.index') }}" class="inline-block bg-primary hover:bg-primary/90 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                Sfoglia i corsi disponibili
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($enrolledCourses as $enrollment)
                @php $course = $enrollment->course; @endphp
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    @if($course->image_url)
                        <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progresso</span>
                                <span>{{ $enrollment->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary h-2.5 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                {{ $enrollment->completed_lessons_count }} di {{ $enrollment->total_lessons_count }} lezioni completate
                            </span>
                            
                            <a href="{{ route('courses.show', $course) }}" 
                               class="text-primary hover:text-primary/80 font-medium">
                                Continua →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

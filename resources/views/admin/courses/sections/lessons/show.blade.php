<x-layouts.admin>
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $lesson->title }}</h1>
            <p class="text-gray-600 mt-2">Sezione: {{ $section->name }} - Corso: {{ $course->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.courses.sections.lessons.edit', [$course, $section, $lesson]) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Modifica
            </a>
            <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Torna alle Lezioni
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lesson Details -->
        <div class="lg:col-span-2">
            <!-- Basic Info Card -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Informazioni Lezione</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Titolo</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $lesson->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ordine</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $lesson->lesson_order }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Durata</label>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $lesson->duration_minutes }} minuti</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Stato</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $lesson->is_active ? 'Attiva' : 'Inattiva' }}
                        </span>
                    </div>
                </div>

                @if($lesson->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Descrizione</label>
                        <p class="text-gray-900">{{ $lesson->description }}</p>
                    </div>
                @endif

                @if($lesson->content)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Contenuto</label>
                        <div class="prose max-w-none text-gray-900">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Video Section -->
            @if($lesson->video_url)
                <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Video della Lezione</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-2">URL Video</label>
                        <a href="{{ $lesson->video_url }}" target="_blank" class="text-primary hover:text-primary/90 break-all">
                            {{ $lesson->video_url }}
                        </a>
                    </div>

                    <!-- Video Preview (if it's a supported format) -->
                    @if(Str::contains($lesson->video_url, ['youtube.com', 'youtu.be']))
                        @php
                            $videoId = null;
                            if (Str::contains($lesson->video_url, 'youtube.com/watch?v=')) {
                                $videoId = Str::after($lesson->video_url, 'v=');
                                $videoId = Str::before($videoId, '&');
                            } elseif (Str::contains($lesson->video_url, 'youtu.be/')) {
                                $videoId = Str::after($lesson->video_url, 'youtu.be/');
                                $videoId = Str::before($videoId, '?');
                            }
                        @endphp
                        
                        @if($videoId)
                            <div class="aspect-w-16 aspect-h-9">
                                <iframe 
                                    src="https://www.youtube.com/embed/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    class="w-full h-64 rounded-lg">
                                </iframe>
                            </div>
                        @endif
                    @elseif(Str::contains($lesson->video_url, 'vimeo.com'))
                        @php
                            $videoId = Str::after($lesson->video_url, 'vimeo.com/');
                            $videoId = Str::before($videoId, '?');
                        @endphp
                        
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe 
                                src="https://player.vimeo.com/video/{{ $videoId }}" 
                                frameborder="0" 
                                allow="autoplay; fullscreen; picture-in-picture" 
                                allowfullscreen
                                class="w-full h-64 rounded-lg">
                            </iframe>
                        </div>
                    @elseif(Str::endsWith($lesson->video_url, ['.mp4', '.webm', '.ogg']))
                        <video controls class="w-full h-64 rounded-lg">
                            <source src="{{ $lesson->video_url }}" type="video/mp4">
                            Il tuo browser non supporta il tag video.
                        </video>
                    @else
                        <div class="bg-gray-100 rounded-lg p-4 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Anteprima video non disponibile</p>
                            <a href="{{ $lesson->video_url }}" target="_blank" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary bg-primary/10 hover:bg-primary/20">
                                Apri Video
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Progress Statistics -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistiche Progresso</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">{{ $lesson->progress_count }}</div>
                        <div class="text-gray-600">Visualizzazioni</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $lesson->progress ? $lesson->progress->where('is_completed', true)->count() : 0 }}
                        </div>
                        <div class="text-gray-600">Completamenti</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            @if($lesson->progress_count > 0 && $lesson->progress)
                                {{ round(($lesson->progress->where('is_completed', true)->count() / $lesson->progress_count) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="text-gray-600">Tasso Completamento</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Context Info -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informazioni Contesto</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Corso</label>
                        <p class="mt-1 font-medium text-gray-900">{{ $course->name }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $course->is_active ? 'Attivo' : 'Inattivo' }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Sezione</label>
                        <p class="mt-1 font-medium text-gray-900">{{ $section->name }}</p>
                        <p class="text-sm text-gray-600">Ordine: {{ $section->section_order }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $section->is_active ? 'Attiva' : 'Inattiva' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Azioni Rapide</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.courses.sections.lessons.edit', [$course, $section, $lesson]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifica Lezione
                    </a>
                    
                    <a href="{{ route('admin.courses.sections.lessons.create', [$course, $section]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nuova Lezione
                    </a>
                    
                    <form action="{{ route('admin.courses.sections.lessons.destroy', [$course, $section, $lesson]) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questa lezione?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Elimina Lezione
                        </button>
                    </form>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Metadata</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Creata:</span>
                        <span class="font-medium">{{ $lesson->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Modificata:</span>
                        <span class="font-medium">{{ $lesson->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID:</span>
                        <span class="font-medium">#{{ $lesson->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Breadcrumb -->
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Corsi
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.show', $course) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">{{ Str::limit($course->name, 30) }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.sections.show', [$course, $section]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">{{ Str::limit($section->name, 30) }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">Lezioni</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($lesson->title, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>
</x-layouts.admin>

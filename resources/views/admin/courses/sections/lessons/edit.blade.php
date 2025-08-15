<x-layouts.admin>
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifica Lezione</h1>
            <p class="text-gray-600 mt-2">{{ $lesson->title }} - Sezione: {{ $section->name }} - Corso: {{ $course->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.courses.sections.lessons.show', [$course, $section, $lesson]) }}" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                Visualizza
            </a>
            <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Torna alle Lezioni
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.courses.sections.lessons.update', [$course, $section, $lesson]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Titolo -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titolo Lezione *</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $lesson->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('title') border-red-500 @enderror"
                           placeholder="Es: Introduzione agli esercizi base"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ordine -->
                <div>
                    <label for="lesson_order" class="block text-sm font-medium text-gray-700 mb-2">Ordine *</label>
                    <input type="number" 
                           name="lesson_order" 
                           id="lesson_order" 
                           value="{{ old('lesson_order', $lesson->lesson_order) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('lesson_order') border-red-500 @enderror"
                           required>
                    @error('lesson_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Ordine di visualizzazione della lezione nella sezione</p>
                </div>

                <!-- Durata -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Durata (minuti) *</label>
                    <input type="number" 
                           name="duration_minutes" 
                           id="duration_minutes" 
                           value="{{ old('duration_minutes', $lesson->duration_minutes) }}"
                           min="1"
                           max="300"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('duration_minutes') border-red-500 @enderror"
                           placeholder="Es: 15"
                           required>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL Video -->
                <div class="md:col-span-2">
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">URL Video</label>
                    <input type="url" 
                           name="video_url" 
                           id="video_url" 
                           value="{{ old('video_url', $lesson->video_url) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('video_url') border-red-500 @enderror"
                           placeholder="https://www.youtube.com/watch?v=...">
                    @error('video_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">URL del video della lezione (YouTube, Vimeo, etc.)</p>
                </div>

                <!-- Stato -->
                <div class="flex items-center md:col-span-2">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', $lesson->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700">Lezione Attiva</label>
                        <p class="text-gray-500">Se disattivata, la lezione non sarà visibile agli studenti</p>
                    </div>
                </div>

                <!-- Descrizione -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('description') border-red-500 @enderror"
                              placeholder="Descrizione dettagliata della lezione...">{{ old('description', $lesson->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contenuto -->
                <div class="md:col-span-2">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Contenuto della Lezione</label>
                    <textarea name="content" 
                              id="content" 
                              rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('content') border-red-500 @enderror"
                              placeholder="Contenuto dettagliato della lezione, istruzioni, note per gli studenti...">{{ old('content', $lesson->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Video Preview -->
            @if($lesson->video_url)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Video Attuale</h3>
                    <div class="mb-4">
                        <a href="{{ $lesson->video_url }}" target="_blank" class="text-primary hover:text-primary/80 break-all text-sm">
                            {{ $lesson->video_url }}
                        </a>
                    </div>

                    <!-- Video Preview (if supported) -->
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
                                    class="w-full h-48 rounded-lg">
                                </iframe>
                            </div>
                        @endif
                    @elseif(Str::endsWith($lesson->video_url, ['.mp4', '.webm', '.ogg']))
                        <video controls class="w-full h-48 rounded-lg">
                            <source src="{{ $lesson->video_url }}" type="video/mp4">
                            Il tuo browser non supporta il tag video.
                        </video>
                    @endif
                </div>
            @endif

            <!-- Section & Course Info -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Informazioni Sezione e Corso</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Corso:</span>
                        <span class="ml-2 font-medium">{{ $course->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Sezione:</span>
                        <span class="ml-2 font-medium">{{ $section->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ordine sezione:</span>
                        <span class="ml-2 font-medium">{{ $section->section_order }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Altre lezioni:</span>
                        <span class="ml-2 font-medium">{{ $section->lessons->where('id', '!=', $lesson->id)->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Progress Info -->
            <div class="mt-6 p-4 bg-accent/10 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Statistiche Progresso</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Visualizzazioni:</span>
                        <span class="ml-2 font-medium text-primary">{{ $lesson->progress_count }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Completamenti:</span>
                        <span class="ml-2 font-medium text-green-600">{{ $lesson->progress ? $lesson->progress->where('is_completed', true)->count() : 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Tasso completamento:</span>
                        <span class="ml-2 font-medium text-purple-600">
                            @if($lesson->progress_count > 0 && $lesson->progress)
                                {{ round(($lesson->progress->where('is_completed', true)->count() / $lesson->progress_count) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Error Summary -->
            @if ($errors->any())
                <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Ci sono errori nel form:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.courses.sections.lessons.show', [$course, $section, $lesson]) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annulla
                </a>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                    Aggiorna Lezione
                </button>
            </div>
        </form>
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
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.courses.sections.lessons.show', [$course, $section, $lesson]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">{{ Str::limit($lesson->title, 30) }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Modifica</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus sul campo titolo
    document.getElementById('title').focus();
    
    // Validazione client-side per l'ordine
    const orderInput = document.getElementById('lesson_order');
    orderInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        }
    });
    
    // Validazione client-side per la durata
    const durationInput = document.getElementById('duration_minutes');
    durationInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        } else if (this.value > 300) {
            this.value = 300;
        }
    });
});
</script>
</x-layouts.admin>

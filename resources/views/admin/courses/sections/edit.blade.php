<x-layouts.admin>
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifica Sezione</h1>
            <p class="text-gray-600 mt-2">{{ $section->name }} - Corso: {{ $course->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.courses.sections.show', [$course, $section]) }}" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                Visualizza
            </a>
            <a href="{{ route('admin.courses.sections.index', $course) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Torna alle Sezioni
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.courses.sections.update', [$course, $section]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Sezione *</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $section->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror"
                           placeholder="Es: Introduzione al corso"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ordine -->
                <div>
                    <label for="section_order" class="block text-sm font-medium text-gray-700 mb-2">Ordine *</label>
                    <input type="number" 
                           name="section_order" 
                           id="section_order" 
                           value="{{ old('section_order', $section->section_order) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('section_order') border-red-500 @enderror"
                           required>
                    @error('section_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Ordine di visualizzazione della sezione nel corso</p>
                </div>

                <!-- Stato -->
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700">Sezione Attiva</label>
                        <p class="text-gray-500">Se disattivata, la sezione non sarà visibile agli studenti</p>
                    </div>
                </div>

                <!-- Descrizione -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrizione</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary @error('description') border-red-500 @enderror"
                              placeholder="Descrizione dettagliata della sezione...">{{ old('description', $section->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Section Info -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Informazioni Sezione Attuale</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Corso:</span>
                        <span class="ml-2 font-medium">{{ $course->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Lezioni:</span>
                        <span class="ml-2 font-medium">{{ $section->lessons->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Durata totale:</span>
                        <span class="ml-2 font-medium">{{ $section->lessons->sum('duration_minutes') }} min</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Stato corso:</span>
                        <span class="ml-2 font-medium {{ $course->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $course->is_active ? 'Attivo' : 'Inattivo' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Lessons Preview -->
            @if($section->lessons->count() > 0)
                <div class="mt-6 p-4 bg-primary/5 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Lezioni in questa Sezione ({{ $section->lessons->count() }})</h3>
                    <div class="space-y-2">
                        @foreach($section->lessons->sortBy('lesson_order')->take(5) as $lesson)
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary mr-2">
                                        {{ $lesson->lesson_order }}
                                    </span>
                                    <span class="font-medium">{{ $lesson->title }}</span>
                                    <span class="ml-2 text-gray-500">({{ $lesson->duration_minutes }} min)</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $lesson->is_active ? 'Attiva' : 'Inattiva' }}
                                </span>
                            </div>
                        @endforeach
                        @if($section->lessons->count() > 5)
                            <div class="text-sm text-gray-500 text-center">
                                ... e altre {{ $section->lessons->count() - 5 }} lezioni
                            </div>
                        @endif
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.courses.sections.lessons.index', [$course, $section]) }}" class="inline-flex items-center text-primary hover:text-primary/80 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Gestisci Lezioni
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Nessuna lezione</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Questa sezione non contiene ancora lezioni. Aggiungi delle lezioni per rendere la sezione completa.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.courses.sections.lessons.create', [$course, $section]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200">
                                    Aggiungi Prima Lezione
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                <a href="{{ route('admin.courses.sections.show', [$course, $section]) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annulla
                </a>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                    Aggiorna Sezione
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
                        <a href="{{ route('admin.courses.sections.index', $course) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary md:ml-2">Sezioni</a>
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
    // Auto-focus sul campo nome
    document.getElementById('name').focus();
    
    // Validazione client-side per l'ordine
    const orderInput = document.getElementById('section_order');
    orderInput.addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        }
    });
});
</script>
</x-layouts.admin>

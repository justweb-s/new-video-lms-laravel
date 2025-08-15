<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dettagli Corso: {{ $course->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.courses.sections.index', $course) }}" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                    Gestisci Sezioni
                </a>
                @if($course->workoutCard)
                    <a href="{{ route('admin.workout-cards.show', $course->workoutCard) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Gestisci Scheda
                    </a>
                @else
                    <a href="{{ route('admin.workout-cards.create', ['course_id' => $course->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Aggiungi Scheda
                    </a>
                @endif
                <a href="{{ route('admin.courses.edit', $course) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Modifica
                </a>
                <a href="{{ route('admin.courses.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Torna alla Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Course Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                @if($course->image_url)
                                    <img src="{{ Storage::url($course->image_url) }}" alt="{{ $course->name }}" class="w-32 h-32 object-cover rounded-lg">
                                @else
                                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-500">Nessuna immagine</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $course->name }}</h3>
                                    <div class="flex items-center space-x-4 mb-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $course->is_active ? 'Attivo' : 'Inattivo' }}
                                        </span>
                                        <span class="text-sm text-gray-500">Creato il {{ $course->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Prezzo:</span>
                                            <span class="text-gray-900">€{{ number_format($course->price, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Durata:</span>
                                            <span class="text-gray-900">{{ $course->duration_weeks ?? 'N/A' }} settimane</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Sezioni:</span>
                                            <span class="text-gray-900">{{ $course->sections_count }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Lezioni:</span>
                                            <span class="text-gray-900">{{ $course->total_lessons }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($course->description)
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">Descrizione</h4>
                                    <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                                </div>
                            @endif

                            @if($course->prerequisites)
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">Prerequisiti</h4>
                                    <p class="text-gray-700 leading-relaxed">{{ $course->prerequisites }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sections -->
                    @if($course->sections->count() > 0)
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Sezioni del Corso</h4>
                                <div class="space-y-4">
                                    @foreach($course->sections as $section)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h5 class="font-medium text-gray-900">{{ $section->name }}</h5>
                                                    @if($section->description)
                                                        <p class="text-sm text-gray-600 mt-1">{{ $section->description }}</p>
                                                    @endif
                                                    <p class="text-xs text-gray-500 mt-2">{{ $section->lessons_count }} lezioni</p>
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $section->is_active ? 'Attiva' : 'Inattiva' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Statistiche</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Studenti iscritti:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $course->enrollments_count }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Sezioni:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $course->sections_count }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Lezioni totali:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $course->total_lessons }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Workout Cards:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $course->workoutCard ? '1' : '0' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enrollments -->
                    @if($course->enrollments->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Iscrizioni Recenti</h4>
                                <div class="space-y-3">
                                    @foreach($course->enrollments->take(5) as $enrollment)
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $enrollment->user->full_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $enrollment->enrolled_at->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Azioni</h4>
                            <div class="space-y-2">
                                @if($course->workoutCard)
                                    <a href="{{ route('admin.workout-cards.show', $course->workoutCard) }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                        Gestisci Scheda
                                    </a>
                                @else
                                    <a href="{{ route('admin.workout-cards.create', ['course_id' => $course->id]) }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                        Aggiungi Scheda
                                    </a>
                                @endif
                                <a href="{{ route('admin.courses.edit', $course) }}" class="block w-full text-center bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    Modifica Corso
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo corso?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Elimina Corso
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

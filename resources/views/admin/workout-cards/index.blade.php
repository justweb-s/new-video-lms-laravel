<x-layouts.admin>
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Schede di Allenamento</h1>
            <p class="text-gray-600 mt-2">Gestisci le schede di allenamento associate ai corsi</p>
        </div>
        <a href="{{ route('admin.workout-cards.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nuova Scheda
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Workout Cards Grid -->
    @if($workoutCards->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($workoutCards as $workoutCard)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $workoutCard->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $workoutCard->course->name }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workoutCard->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $workoutCard->is_active ? 'Attiva' : 'Inattiva' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-4">
                        @if($workoutCard->description)
                            <p class="text-gray-700 text-sm mb-4">{{ Str::limit($workoutCard->description, 100) }}</p>
                        @endif

                        <!-- Details -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Livello:</span>
                                <span class="font-medium capitalize {{ 
                                    $workoutCard->difficulty_level === 'beginner' ? 'text-green-600' : 
                                    ($workoutCard->difficulty_level === 'intermediate' ? 'text-yellow-600' : 'text-red-600') 
                                }}">
                                    {{ ucfirst($workoutCard->difficulty_level) }}
                                </span>
                            </div>
                            
                            @if($workoutCard->estimated_duration)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Durata:</span>
                                    <span class="font-medium">{{ $workoutCard->estimated_duration }} min</span>
                                </div>
                            @endif

                            @if($workoutCard->equipment_needed)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Attrezzatura:</span>
                                    <span class="font-medium">{{ Str::limit($workoutCard->equipment_needed, 30) }}</span>
                                </div>
                            @endif

                            @if($workoutCard->exercises)
                                @php
                                    $exercises = json_decode($workoutCard->exercises, true);
                                    $exerciseCount = is_array($exercises) ? count($exercises) : 0;
                                @endphp
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Esercizi:</span>
                                    <span class="font-medium">{{ $exerciseCount }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- PDF Link -->
                        @if($workoutCard->pdf_url)
                            <div class="mt-4">
                                <a href="{{ $workoutCard->pdf_url }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Visualizza PDF
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                Creata {{ $workoutCard->created_at->format('d/m/Y') }}
                            </span>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.workout-cards.show', $workoutCard) }}" class="text-blue-600 hover:text-blue-900" title="Visualizza">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.workout-cards.edit', $workoutCard) }}" class="text-indigo-600 hover:text-indigo-900" title="Modifica">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.workout-cards.destroy', $workoutCard) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa scheda di allenamento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Elimina">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $workoutCards->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna scheda di allenamento</h3>
            <p class="mt-1 text-sm text-gray-500">Inizia creando la prima scheda di allenamento per i tuoi corsi.</p>
            <div class="mt-6">
                <a href="{{ route('admin.workout-cards.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nuova Scheda
                </a>
            </div>
        </div>
    @endif
</div>
</x-layouts.admin>

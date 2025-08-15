<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Scheda di Allenamento</h2>
                <p class="text-sm text-gray-500">{{ $workoutCard->title }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.workout-cards.edit', $workoutCard) }}" class="text-sm bg-primary hover:bg-primary/90 text-white px-3 py-2 rounded">Modifica</a>
                <a href="{{ route('admin.workout-cards.index') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded">Elenco</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Dettaglio principale -->
                <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workoutCard->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $workoutCard->is_active ? 'ATTIVA' : 'INATTIVA' }}
                            </span>
                            <span class="text-sm text-gray-500">Creata: {{ $workoutCard->created_at?->format('d/m/Y H:i') }}</span>
                            @if($workoutCard->updated_at)
                                <span class="text-sm text-gray-500">Aggiornata: {{ $workoutCard->updated_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Corso</h3>
                            @if($workoutCard->course)
                                <a class="mt-1 inline-block text-primary hover:underline" href="{{ route('admin.courses.show', $workoutCard->course_id) }}">{{ $workoutCard->course->name }}</a>
                            @else
                                <p class="mt-1 text-gray-500">N/A</p>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Titolo</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $workoutCard->title }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Contenuto</h3>
                            <div class="mt-1 prose prose-sm max-w-none">{!! nl2br(e($workoutCard->content)) !!}</div>
                        </div>

                        @if($workoutCard->warmup)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Riscaldamento (warm-up)</h3>
                                <div class="mt-1 prose prose-sm max-w-none">{!! nl2br(e($workoutCard->warmup)) !!}</div>
                            </div>
                        @endif

                        @if($workoutCard->venous_return)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Defaticamento (venous return)</h3>
                                <div class="mt-1 prose prose-sm max-w-none">{!! nl2br(e($workoutCard->venous_return)) !!}</div>
                            </div>
                        @endif

                        @if($workoutCard->notes)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Note</h3>
                                <div class="mt-1 prose prose-sm max-w-none">{!! nl2br(e($workoutCard->notes)) !!}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Studenti iscritti al corso -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800">Studenti iscritti</h3>
                        @if($workoutCard->course && $workoutCard->course->enrollments->count())
                            <ul class="mt-4 divide-y divide-gray-200">
                                @foreach($workoutCard->course->enrollments as $enrollment)
                                    <li class="py-2 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $enrollment->user->full_name ?? $enrollment->user->email }}</p>
                                            <p class="text-xs text-gray-500">Iscritto: {{ optional($enrollment->enrolled_at)->format('d/m/Y') }}</p>
                                        </div>
                                        <a href="{{ route('admin.students.show', $enrollment->user_id) }}" class="text-sm text-primary hover:text-primary/80">Dettagli</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-sm text-gray-500">Nessuno studente iscritto o corso non disponibile.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

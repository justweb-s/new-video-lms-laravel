<x-layouts.student>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Scheda di Allenamento</h1>
            <p class="text-sm text-gray-500">Corso: {{ $course->title ?? $course->name }}</p>
        </div>
        <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200">
            ← Torna al corso
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6 space-y-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workoutCard->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $workoutCard->is_active ? 'ATTIVA' : 'INATTIVA' }}
                </span>
                <span class="text-sm text-gray-500">Aggiornata: {{ optional($workoutCard->updated_at ?? $workoutCard->created_at)->format('d/m/Y H:i') }}</span>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $workoutCard->title }}</h2>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500">Contenuto</h3>
                <div class="mt-1">
                    <link rel="stylesheet" href="{{ asset('css/workout-card.css') }}">
                    <div class="workout-card-content">{!! $workoutCard->content !!}</div>
                </div>
            </div>

            @if($workoutCard->warmup)
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Riscaldamento (warm-up)</h3>
                    <div class="mt-1 prose max-w-none">{!! nl2br(e($workoutCard->warmup)) !!}</div>
                </div>
            @endif

            @if($workoutCard->venous_return)
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Defaticamento (venous return)</h3>
                    <div class="mt-1 prose max-w-none">{!! nl2br(e($workoutCard->venous_return)) !!}</div>
                </div>
            @endif

            @if($workoutCard->notes)
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Note</h3>
                    <div class="mt-1 prose max-w-none">{!! nl2br(e($workoutCard->notes)) !!}</div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-2">Info Corso</h3>
            <p class="text-sm text-gray-600">{{ $course->description }}</p>
            <div class="mt-4">
                <a href="{{ route('courses.show', $course) }}" class="inline-block w-full text-center bg-primary hover:bg-primary/90 text-white font-medium py-2 px-4 rounded-md">
                    Vai al corso
                </a>
            </div>
        </div>
    </div>
</div>
</x-layouts.student>

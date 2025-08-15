<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifica Scheda di Allenamento</h2>
                <p class="text-sm text-gray-500">Aggiorna i dettagli della scheda collegata al corso</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.workout-cards.show', $workoutCard) }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded">Torna al dettaglio</a>
                <a href="{{ route('admin.workout-cards.index') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded">Elenco</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-300 bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-700 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.workout-cards.update', $workoutCard) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Corso</label>
                        <select name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id', $workoutCard->course_id) == $course->id)>{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titolo</label>
                        <input type="text" name="title" value="{{ old('title', $workoutCard->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contenuto</label>
                        <textarea name="content" rows="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" required>{{ old('content', $workoutCard->content) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Descrizione principale della scheda: esercizi, serie, ripetizioni, tempi, ecc.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Riscaldamento (warm-up)</label>
                            <textarea name="warmup" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('warmup', $workoutCard->warmup) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Defaticamento (venous return)</label>
                            <textarea name="venous_return" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('venous_return', $workoutCard->venous_return) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Note</label>
                        <textarea name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('notes', $workoutCard->notes) }}</textarea>
                    </div>

                    <div class="flex items-center">
                        <input id="is_active" type="checkbox" name="is_active" value="1" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary" @checked(old('is_active', $workoutCard->is_active)) />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Attiva</label>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.workout-cards.show', $workoutCard) }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200">Annulla</a>
                        <button type="submit" class="px-4 py-2 rounded bg-primary text-white hover:bg-primary/90">Salva modifiche</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>

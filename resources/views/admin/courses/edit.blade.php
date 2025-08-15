<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifica Corso: {{ $course->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.courses.show', $course) }}" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                    Visualizza
                </a>
                <a href="{{ route('admin.courses.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Torna alla Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome Corso</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $course->name) }}" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Descrizione</label>
                                <textarea name="description" id="description" rows="4" 
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('description', $course->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Current Image -->
                            @if($course->image_url)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Immagine Attuale</label>
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ Storage::url($course->image_url) }}" alt="{{ $course->name }}" class="w-24 h-24 object-cover rounded-lg">
                                        <div>
                                            <p class="text-sm text-gray-600">Immagine corrente del corso</p>
                                            <p class="text-xs text-gray-500">Carica una nuova immagine per sostituirla</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- New Image -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700">{{ $course->image_url ? 'Nuova Immagine' : 'Immagine Corso' }}</label>
                                <input type="file" name="image" id="image" accept="image/*" 
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Prezzo (€)</label>
                                <input type="number" name="price" id="price" value="{{ old('price', $course->price) }}" min="0" step="0.01" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration Weeks -->
                            <div>
                                <label for="duration_weeks" class="block text-sm font-medium text-gray-700">Durata (settimane)</label>
                                <input type="number" name="duration_weeks" id="duration_weeks" value="{{ old('duration_weeks', $course->duration_weeks) }}" min="1" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @error('duration_weeks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Is Active -->
                            <div>
                                <div class="flex items-center h-full">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }} 
                                           class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Corso attivo
                                    </label>
                                </div>
                                @error('is_active')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prerequisites -->
                            <div class="md:col-span-2">
                                <label for="prerequisites" class="block text-sm font-medium text-gray-700">Prerequisiti</label>
                                <textarea name="prerequisites" id="prerequisites" rows="3" 
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ old('prerequisites', $course->prerequisites) }}</textarea>
                                @error('prerequisites')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 mt-6">
                            <a href="{{ route('admin.courses.show', $course) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Annulla
                            </a>
                            <button type="submit" class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded">
                                Aggiorna Corso
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Course Statistics -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiche Corso</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary">{{ $course->enrollments_count }}</div>
                            <div class="text-sm text-gray-600">Studenti Iscritti</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $course->sections_count }}</div>
                            <div class="text-sm text-gray-600">Sezioni</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary">{{ $course->total_lessons }}</div>
                            <div class="text-sm text-gray-600">Lezioni</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $course->workoutCard ? '1' : '0' }}</div>
                            <div class="text-sm text-gray-600">Workout Cards</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

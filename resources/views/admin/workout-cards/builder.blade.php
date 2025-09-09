<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-dumbbell mr-2"></i>Builder Scheda Allenamento
                </h2>
                <p class="text-sm text-gray-500 mt-1">Corso: {{ $course->name }}</p>
            </div>
            <div class="flex gap-2">
                @if($workoutCard)
                    <a href="{{ route('admin.workout-cards.show', $workoutCard) }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded">Visualizza</a>
                @endif
                <a href="{{ route('admin.workout-cards.index') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded">Torna all'elenco</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        cardTitle: '{{ $cardTitle }}',
        headerLogoUrl: '{{ $data['header_logo_url'] ?? '' }}',
        infoBoxScadenza: '{{ $data['info_box_scadenza'] ?? '' }}',
        infoBoxCheck: '{{ $data['info_box_check'] ?? '' }}',
        workouts: {{ json_encode($data['workouts'] ?? []) }},

        addWorkout() {
            this.workouts.push({
                title: `WORKOUT ${this.workouts.length + 1}`,
                warmup: '',
                venous_return: '',
                exercises: []
            });
        },

        removeWorkout(index) {
            if (confirm('Sei sicuro di voler rimuovere questo workout?')) {
                this.workouts.splice(index, 1);
            }
        },

        addExercise(workoutIndex) {
            this.workouts[workoutIndex].exercises.push({
                name: '',
                series: '',
                reps: '',
                rest: '',
                note: ''
            });
        },

        removeExercise(workoutIndex, exerciseIndex) {
            this.workouts[workoutIndex].exercises.splice(exerciseIndex, 1);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-700 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.workout-cards.store-builder') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <!-- Informazioni Generali -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heading text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Informazioni Generali</h3>
                    </div>
                    <div class="form-group">
                        <label for="card_title" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1"></i> Titolo Scheda
                        </label>
                        <input type="text" id="card_title" name="card_title" x-model="cardTitle" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Inserisci il titolo della scheda..." required>
                    </div>
                </div>

                <!-- Intestazione e Info Box -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 rounded-lg shadow-md p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Intestazione e Info Box</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-image mr-1"></i> URL Logo
                            </label>
                            <input type="url" name="header_logo_url" x-model="headerLogoUrl" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="https://esempio.com/logo.png">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i> Testo Scadenza
                            </label>
                            <input type="text" name="info_box_scadenza" x-model="infoBoxScadenza" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Es: 30 giorni dalla data di inizio">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-check-circle mr-1"></i> Testo Check
                            </label>
                            <input type="text" name="info_box_check" x-model="infoBoxCheck" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Es: Completare tutti gli esercizi">
                        </div>
                    </div>
                </div>

                <!-- Workouts Container -->
                <div id="workouts-container">
                    <template x-for="(workout, workoutIndex) in workouts" :key="workoutIndex">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg shadow-md p-6 relative">
                            <button type="button" @click="removeWorkout(workoutIndex)" 
                                    class="absolute top-4 right-4"
                                    style="width:42px; height:42px; background:#dc2626; border:2px solid #7f1d1d; color:#fff; border-radius:9999px; box-shadow:0 8px 18px rgba(220,38,38,0.4);"
                                    title="Rimuovi workout" aria-label="Rimuovi workout">
                                <i class="fas fa-times" style="font-size:18px;"></i>
                            </button>
                            
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-fire text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800" x-text="'Workout ' + (workoutIndex + 1)"></h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-edit mr-1"></i> Titolo Workout
                                    </label>
                                    <input type="text" :name="'workouts[' + workoutIndex + '][title]'" x-model="workout.title" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           :placeholder="'WORKOUT ' + (workoutIndex + 1)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-thermometer-half mr-1"></i> Riscaldamento
                                    </label>
                                    <textarea :name="'workouts[' + workoutIndex + '][warmup]'" x-model="workout.warmup" rows="3" 
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                              placeholder="Descrivi il riscaldamento per questo workout..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Esercizi -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="flex items-center gap-2 text-md font-semibold text-gray-800">
                                        <i class="fas fa-list-ol"></i>
                                        Esercizi
                                    </h4>
                                    <button type="button" @click="addExercise(workoutIndex)" 
                                        class="w-full flex items-center justify-center gap-3 p-4 rounded-lg font-semibold"
                                        style="background:#36583c; color:#f4e648; border:2px solid #f4e648; box-shadow:0 8px 18px rgba(54,88,60,0.35);">
                                    <i class="fas fa-plus-circle text-lg"></i>
                                    <span>Aggiungi Esercizio</span>
                                </button>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    <div class="min-w-full">
                                        <!-- Header -->
                                        <div class="grid grid-cols-6 gap-2 p-3 bg-gray-100 rounded-t-lg text-sm font-medium text-gray-700">
                                            <div>Nome Esercizio</div>
                                            <div>Serie</div>
                                            <div>Ripetizioni</div>
                                            <div>Rest e T.U.T</div>
                                            <div>Note</div>
                                            <div>Azioni</div>
                                        </div>
                                        
                                        <!-- Exercises -->
                                        <template x-for="(exercise, exerciseIndex) in workout.exercises" :key="exerciseIndex">
                                            <div class="grid grid-cols-6 gap-2 p-3 bg-white border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                                <input type="text" :name="'workouts[' + workoutIndex + '][exercises][' + exerciseIndex + '][name]'" 
                                                       x-model="exercise.name" placeholder="Nome Esercizio" 
                                                       class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                <input type="text" :name="'workouts[' + workoutIndex + '][exercises][' + exerciseIndex + '][series]'" 
                                                       x-model="exercise.series" placeholder="Serie" 
                                                       class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                <input type="text" :name="'workouts[' + workoutIndex + '][exercises][' + exerciseIndex + '][reps]'" 
                                                       x-model="exercise.reps" placeholder="Ripetizioni" 
                                                       class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                <input type="text" :name="'workouts[' + workoutIndex + '][exercises][' + exerciseIndex + '][rest]'" 
                                                       x-model="exercise.rest" placeholder="Rest e T.U.T" 
                                                       class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                <input type="text" :name="'workouts[' + workoutIndex + '][exercises][' + exerciseIndex + '][note]'" 
                                                       x-model="exercise.note" placeholder="Note" 
                                                       class="px-2 py-1 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                <button type="button" @click="removeExercise(workoutIndex, exerciseIndex)" 
                                                        class="text-white rounded-lg shadow-md text-sm flex items-center justify-center"
                                                        style="width:38px; height:38px; background:#dc2626; border-radius:10px; box-shadow:0 4px 10px rgba(220,38,38,0.4); transition:transform .15s ease;"
                                                        title="Rimuovi esercizio" aria-label="Rimuovi esercizio">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                        <path d="M10 11v6"></path>
                                                        <path d="M14 11v6"></path>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        
                                        <div x-show="workout.exercises.length === 0" class="p-4 bg-gray-50 text-center text-gray-500 text-sm rounded-b-lg">
                                            Nessun esercizio aggiunto. Clicca "Aggiungi Esercizio" per iniziare.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-heart mr-1"></i> Ritorno Venoso
                                </label>
                                <textarea :name="'workouts[' + workoutIndex + '][venous_return]'" x-model="workout.venous_return" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                          placeholder="Descrivi gli esercizi per il ritorno venoso..."></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Add Workout Button -->
                <button type="button" @click="addWorkout()" 
                        class="w-full flex items-center justify-center gap-3 p-6 text-white rounded-lg shadow-lg"
                        style="background:#059669; border:2px solid #065f46;"
                        title="Aggiungi Workout">
                    <i class="fas fa-plus-circle text-xl"></i>
                    <span class="text-lg font-semibold">Aggiungi Nuovo Workout</span>
                </button>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-center gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="text-white font-bold rounded-lg"
                            style="padding:14px 28px; background:#2563eb; border:2px solid #1d4ed8; box-shadow:0 8px 20px rgba(29,78,216,.35);">
                        <i class="fas fa-save mr-2"></i> Salva Scheda
                    </button>
                    <a href="{{ route('admin.workout-cards.index') }}" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Annulla
                    </a>
                </div>
            </form>
        </div>
    </div>


</x-layouts.admin>

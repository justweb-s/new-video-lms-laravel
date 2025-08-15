<x-layouts.admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestione Iscrizioni: {{ $student->full_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.students.show', $student) }}" class="bg-primary hover:bg-primary/90 focus:bg-primary/90 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                    Visualizza Studente
                </a>
                <a href="{{ route('admin.students.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Torna alla Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add New Enrollment -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aggiungi Nuova Iscrizione</h3>
                    <form method="POST" action="{{ route('admin.students.enrollments.store', $student) }}" class="flex items-end space-x-4">
                        @csrf
                        <div class="flex-1">
                            <label for="course_id" class="block text-sm font-medium text-gray-700">Corso</label>
                            <select name="course_id" id="course_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-accent">
                                <option value="">Seleziona un corso</option>
                                @foreach($availableCourses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} - €{{ number_format($course->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700">Data Scadenza (opzionale)</label>
                            <input type="date" name="expires_at" id="expires_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-accent">
                        </div>
                        <button type="submit" class="bg-primary hover:bg-primary/90 focus:bg-primary/90 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                            Aggiungi Iscrizione
                        </button>
                    </form>
                </div>
            </div>

            <!-- Current Enrollments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Iscrizioni Attuali</h3>
                    
                    @if($student->enrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Corso</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Iscrizione</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scadenza</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progresso</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($student->enrollments as $enrollment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($enrollment->course->image_url)
                                                        <img src="{{ Storage::url($enrollment->course->image_url) }}" alt="{{ $enrollment->course->name }}" class="h-10 w-10 rounded-lg object-cover mr-3">
                                                    @else
                                                        <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                            <span class="text-xs text-gray-500">IMG</span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment->course->name }}</div>
                                                        <div class="text-sm text-gray-500">€{{ number_format($enrollment->course->price, 2) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $enrollment->enrolled_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $enrollment->expires_at ? $enrollment->expires_at->format('d/m/Y') : 'Nessuna scadenza' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-primary h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-900">{{ number_format($enrollment->progress_percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enrollment->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $enrollment->is_active ? 'Attiva' : 'Inattiva' }}
                                                </span>
                                                @if($enrollment->expires_at && $enrollment->expires_at->isPast())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-1">
                                                        Scaduta
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <form action="{{ route('admin.students.enrollments.toggle', [$student, $enrollment]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                                            {{ $enrollment->is_active ? 'Disattiva' : 'Attiva' }}
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.students.enrollments.destroy', [$student, $enrollment]) }}" method="POST" class="inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa iscrizione?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Elimina</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna iscrizione</h3>
                            <p class="mt-1 text-sm text-gray-500">Questo studente non è ancora iscritto a nessun corso.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

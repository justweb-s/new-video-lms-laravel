<x-layouts.student>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            I Miei Corsi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Corsi Iscritti</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_courses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Corsi Attivi</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_courses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Lezioni Completate</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_lessons_completed'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Progresso Medio</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['average_progress'], 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">I Tuoi Corsi</h3>
                @if(count($enrolledCourses) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($enrolledCourses as $courseData)
                            @php
                                $course = $courseData['course'];
                                $enrollment = $courseData['enrollment'];
                                $progressPercentage = $courseData['progress_percentage'];
                                $isActive = $courseData['is_active'];
                                $isExpired = $courseData['is_expired'];
                            @endphp
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg {{ !$isActive ? 'opacity-60' : '' }}">
                                <div class="relative">
                                    @if($course->image_url)
                                        <img src="{{ Storage::url($course->image_url) }}" alt="{{ $course->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($isExpired)
                                        <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-medium">
                                            Scaduto
                                        </div>
                                    @elseif(!$isActive)
                                        <div class="absolute top-2 right-2 bg-gray-500 text-white px-2 py-1 rounded text-xs font-medium">
                                            Inattivo
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->name }}</h4>
                                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($course->description, 100) }}</p>
                                    
                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                            <span>Progresso</span>
                                            <span>{{ number_format($progressPercentage, 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-between items-center text-sm text-gray-500 mb-1">
                                        <span>{{ $courseData['completed_lessons'] }}/{{ $courseData['total_lessons'] }} lezioni</span>
                                        <span>Iscritto {{ $enrollment->enrolled_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                                        <span></span>
                                        <span>Scadenza: {{ $enrollment->expires_at ? $enrollment->expires_at->format('d/m/Y') : 'Nessuna scadenza' }}</span>
                                    </div>
                                    
                                    @if($isActive && !$isExpired)
                                        <a href="{{ route('courses.show', $course) }}" class="w-full bg-primary hover:bg-primary/90 focus:bg-primary/90 text-white font-bold py-2 px-4 rounded text-center block focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                            Continua Corso
                                        </a>
                                    @else
                                        <div class="w-full bg-gray-400 text-white font-bold py-2 px-4 rounded text-center">
                                            {{ $isExpired ? 'Corso Scaduto' : 'Corso Non Disponibile' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-lg shadow-sm">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun corso disponibile</h3>
                        <p class="mt-1 text-sm text-gray-500">Non sei ancora iscritto a nessun corso. Contatta l'amministratore per l'iscrizione.</p>
                    </div>
                @endif
            </div>

            <!-- Recent Progress -->
            @if($recentProgress->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Progressi Recenti</h3>
                        <div class="space-y-3">
                            @foreach($recentProgress as $progress)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $progress->lesson->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $progress->lesson->section->course->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">{{ $progress->completed_at->diffForHumans() }}</p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completata
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.student>

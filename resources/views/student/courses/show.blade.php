@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6" x-data="{ openWorkout: false }">
        <!-- Left Sidebar - Course Navigation -->
        <div class="w-full md:w-1/4 bg-white rounded-lg shadow-md p-4 h-fit">
            <h2 class="text-xl font-bold mb-4">{{ $course->title }}</h2>
            
            @if($course->workoutCard && $course->workoutCard->is_active)
                <button type="button"
                        @click="openWorkout = !openWorkout"
                        :aria-pressed="openWorkout.toString()"
                        class="w-full inline-flex items-center justify-center mb-3 px-4 py-2 text-sm font-medium rounded-md text-white"
                        :class="openWorkout ? 'bg-gray-600 hover:bg-gray-700' : 'bg-indigo-600 hover:bg-indigo-700'">
                    <span x-show="!openWorkout">Scheda di Allenamento</span>
                    <span x-show="openWorkout">Nascondi Scheda</span>
                </button>
            @endif

            <div class="space-y-2">
                @foreach($course->sections as $section)
                    <div class="border rounded-md overflow-hidden" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                        <button 
                            class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 flex justify-between items-center"
                            @click="open = !open"
                        >
                            <span class="font-medium">{{ $section->name }}</span>
                            <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'transform rotate-180': open }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div class="px-4 py-2 space-y-1" x-show="open" x-transition>
                            @foreach($section->lessons as $lesson)
                                <a href="{{ route('courses.lesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                                   class="block px-3 py-2 text-sm rounded hover:bg-primary/5 {{ (isset($currentLesson) && $currentLesson && $lesson->id === $currentLesson->id) ? 'bg-primary/10 text-primary font-medium' : 'text-gray-700' }}">
                                    {{ $loop->iteration }}. {{ $lesson->title }}
                                    @if(isset($completedLessonIds) && in_array($lesson->id, $completedLessonIds))
                                        <span class="ml-2 text-green-500">✓</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Content - Video Player -->
        <div class="w-full md:w-3/4">
            @if($course->workoutCard && $course->workoutCard->is_active)
            <div x-show="openWorkout" x-transition class="mb-6 bg-white rounded-lg shadow-md p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Scheda di Allenamento</h1>
                        <p class="text-sm text-gray-500 mt-1">Corso: {{ $course->title ?? $course->name }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->workoutCard->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $course->workoutCard->is_active ? 'ATTIVA' : 'INATTIVA' }}
                            </span>
                            <span class="text-xs text-gray-500">Aggiornata: {{ optional($course->workoutCard->updated_at ?? $course->workoutCard->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <button type="button" @click="openWorkout = false" class="ml-4 inline-flex items-center px-3 py-1.5 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Chiudi
                    </button>
                </div>
                <div class="mt-6 space-y-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $course->workoutCard->title }}</h2>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Contenuto</h3>
                        <div class="mt-1 prose max-w-none">{!! nl2br(e($course->workoutCard->content)) !!}</div>
                    </div>
                    @if($course->workoutCard->warmup)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Riscaldamento (warm-up)</h3>
                            <div class="mt-1 prose max-w-none">{!! nl2br(e($course->workoutCard->warmup)) !!}</div>
                        </div>
                    @endif
                    @if($course->workoutCard->venous_return)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Defaticamento (venous return)</h3>
                            <div class="mt-1 prose max-w-none">{!! nl2br(e($course->workoutCard->venous_return)) !!}</div>
                        </div>
                    @endif
                    @if($course->workoutCard->notes)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Note</h3>
                            <div class="mt-1 prose max-w-none">{!! nl2br(e($course->workoutCard->notes)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if(isset($currentLesson) && $currentLesson)
                    <div class="aspect-w-16 aspect-h-9 bg-black">
                        @if($currentLesson->video_url)
                            <video 
                                id="lesson-video"
                                class="w-full" 
                                controls
                                {{-- Add video.js or similar for better player controls --}}
                            >
                                <source src="{{ $currentLesson->video_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <div class="flex items-center justify-center h-full bg-gray-100 text-gray-500">
                                <p>No video available for this lesson</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-4">{{ $currentLesson->title }}</h1>
                        <div class="prose max-w-none">
                            {!! $currentLesson->content !!}
                        </div>
                        
                        <div class="mt-6 pt-4 border-t flex justify-between items-center">
                            @if($previousLesson)
                                <a href="{{ route('courses.lesson', ['course' => $course->id, 'lesson' => $previousLesson->id]) }}" 
                                   class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 flex items-center">
                                    ← Previous
                                </a>
                            @else
                                <div></div>
                            @endif
                            
                            @if($nextLesson)
                                <a href="{{ route('courses.lesson', ['course' => $course->id, 'lesson' => $nextLesson->id]) }}" 
                                   class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 flex items-center">
                                    Next →
                                </a>
                            @else
                                <form action="{{ route('progress.complete') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="lesson_id" value="{{ $currentLesson->id }}">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                        Mark as Complete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p>Select a lesson to begin</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize video player when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('lesson-video');
        if (video) {
            // You can add video.js initialization here if needed
            // videojs('lesson-video', { /* options */ });
            
            // Track video progress reliably every N seconds and mark completion on end
            const routeUrl = '{{ (isset($currentLesson) && $currentLesson) ? route('progress.update') : '' }}';
            const lessonId = {{ (isset($currentLesson) && $currentLesson) ? $currentLesson->id : 'null' }};
            const csrfToken = '{{ csrf_token() }}';
            let lastSentTime = 0;
            const SEND_INTERVAL = 10; // seconds

            function sendProgress(completed = false) {
                if (!routeUrl || !lessonId) return;
                const current = Math.floor(video.currentTime || 0);
                const duration = Math.max(1, Math.floor(video.duration || 0)); // avoid div by 0
                const percent = Math.min(100, Math.round((current / duration) * 100));

                fetch(routeUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        lesson_id: lessonId,
                        watch_time_seconds: current,
                        progress_percentage: percent,
                        completed: completed
                    })
                }).catch(() => {});
            }

            video.addEventListener('timeupdate', function() {
                if (video.paused) return;
                if ((video.currentTime - lastSentTime) >= SEND_INTERVAL) {
                    sendProgress(false);
                    lastSentTime = video.currentTime;
                }
            });

            video.addEventListener('ended', function() {
                // Mark lesson as completed when the video ends
                sendProgress(true);
            });

            document.addEventListener('visibilitychange', function() {
                // Try to persist latest progress when the tab is hidden
                if (document.visibilityState === 'hidden' && video && !video.paused) {
                    sendProgress(false);
                }
            });
        }
    });
</script>
@endpush
@endsection

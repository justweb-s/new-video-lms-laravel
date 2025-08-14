@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Left Sidebar - Course Navigation -->
        <div class="w-full md:w-1/4 bg-white rounded-lg shadow-md p-4 h-fit">
            <h2 class="text-xl font-bold mb-4">{{ $course->title }}</h2>
            
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
                                   class="block px-3 py-2 text-sm rounded hover:bg-blue-50 {{ (isset($currentLesson) && $currentLesson && $lesson->id === $currentLesson->id) ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-700' }}">
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
                                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
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

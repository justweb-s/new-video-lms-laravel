<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        
        // Verify user is enrolled in the course
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->first();
            
        if (!$enrollment || $enrollment->isExpired()) {
            abort(403, 'Non sei iscritto a questo corso o la tua iscrizione è scaduta.');
        }

        // Verify the lesson belongs to the course
        if ($lesson->section->course_id !== $course->id) {
            abort(404);
        }

        // Load necessary relationships
        $course->load(['sections.lessons']);
        
        // Find previous and next lessons for navigation
        $allLessons = $course->sections->flatMap->lessons;
        $currentIndex = $allLessons->search(function ($item) use ($lesson) {
            return $item->id === $lesson->id;
        });

        $previousLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        // Completed lessons for current user (to show ticks in sidebar)
        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->where('completed', true)
            ->pluck('lesson_id')
            ->toArray();

        // Get or create lesson progress
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['completed' => false, 'watch_time_seconds' => 0]
        );

        return view('student.courses.show', [
            'course' => $course,
            'currentLesson' => $lesson,
            'previousLesson' => $previousLesson,
            'nextLesson' => $nextLesson,
            'completedLessonIds' => $completedLessonIds,
        ]);
    }
}

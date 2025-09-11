<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all active enrollments with course and lessons loaded
        $enrollments = $user->enrollments()
            ->with(['course.sections.lessons'])
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->get();

        // Attach computed attributes expected by the view
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $allLessons = $course->sections->flatMap->lessons;
            $totalLessons = $allLessons->count();
            $completedLessons = 0;

            if ($totalLessons > 0) {
                $lessonIds = $allLessons->pluck('id');
                $completedLessons = LessonProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->where('completed', true)
                    ->count();
            }

            $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            // Set dynamic attributes used by the Blade view
            $enrollment->setAttribute('completed_lessons_count', $completedLessons);
            $enrollment->setAttribute('total_lessons_count', $totalLessons);
            $enrollment->setAttribute('progress_percentage', $progressPercentage);
        }

        return view('student.courses.index', [
            'enrolledCourses' => $enrollments
        ]);
    }
    
    public function show(Course $course)
    {
        $user = Auth::user();
        
        // Verifica se l'utente è iscritto al corso
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->first();
            
        if (!$enrollment || $enrollment->isExpired()) {
            abort(403, 'Non sei iscritto a questo corso o la tua iscrizione è scaduta.');
        }

        // Carica il corso con tutte le relazioni necessarie
        $course->load(['sections.lessons', 'workoutCard']);
        
        // Calcola il progresso per ogni lezione
        $lessonsWithProgress = [];
        $allLessons = $course->sections->flatMap->lessons;
        $lessonIds = $allLessons->pluck('id');
        $progressMap = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        foreach ($course->sections as $section) {
            foreach ($section->lessons as $lesson) {
                $progress = $progressMap->get($lesson->id);
                $lessonsWithProgress[] = [
                    'lesson' => $lesson,
                    'progress' => $progress,
                    'completed' => $progress ? $progress->completed : false,
                    'watch_time' => $progress ? $progress->watch_time_seconds : 0,
                ];
            }
        }
        
        // Calcola statistiche del corso
        $totalLessons = $allLessons->count();
        $completedLessons = collect($lessonsWithProgress)->where('completed', true)->count();
        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
        
        return view('student.courses.show', [
            'course' => $course,
            'enrollment' => $enrollment,
            'lessonsWithProgress' => $lessonsWithProgress,
            'totalLessons' => $totalLessons,
            'completedLessons' => $completedLessons,
            'progressPercentage' => $progressPercentage,
            'currentLesson' => null,
            'previousLesson' => null,
            'nextLesson' => null,
        ]);
    }

    public function lesson(Course $course, Lesson $lesson)
    {
        $user = Auth::user();
        
        // Verifica se l'utente è iscritto al corso
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->first();
            
        if (!$enrollment || $enrollment->isExpired()) {
            abort(403, 'Non sei iscritto a questo corso o la tua iscrizione è scaduta.');
        }
        
        // Verifica che la lezione appartenga al corso
        if ($lesson->section->course_id !== $course->id) {
            abort(404);
        }
        
        // Recupera o crea il progresso della lezione
        $progress = LessonProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed' => false,
                'watch_time_seconds' => 0,
                'progress_percentage' => 0,
            ]
        );
        
        // Carica lezione precedente e successiva evitando query aggiuntive
        $course->loadMissing(['sections.lessons']);
        $allLessons = $course->sections->flatMap->lessons->sortBy('lesson_order')->values();
        $currentIndex = $allLessons->search(function($item) use ($lesson) {
            return $item->id === $lesson->id;
        });
        
        $previousLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;
        
        return view('student.courses.lesson', compact(
            'course', 
            'lesson', 
            'progress', 
            'previousLesson', 
            'nextLesson'
        ));
    }

    public function workout(Course $course)
    {
        $user = Auth::user();

        // Verifica se l'utente è iscritto al corso
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->first();

        if (!$enrollment || $enrollment->isExpired()) {
            abort(403, 'Non sei iscritto a questo corso o la tua iscrizione è scaduta.');
        }

        // Carica la scheda di allenamento
        $course->load('workoutCard');

        if (!$course->workoutCard || !$course->workoutCard->is_active) {
            abort(404);
        }

        return view('student.courses.workout', [
            'course' => $course,
            'workoutCard' => $course->workoutCard,
        ]);
    }
}

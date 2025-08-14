<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{
    public function updateProgress(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'watch_time_seconds' => 'required|integer|min:0',
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'completed' => 'boolean'
        ]);

        $user = Auth::user();
        $lesson = Lesson::findOrFail($validated['lesson_id']);
        Log::debug('StudentProgress.updateProgress start', ['user_id' => $user?->id, 'lesson_id' => $lesson->id, 'payload' => $validated]);
        
        // Verifica che l'utente sia iscritto al corso
        $enrollment = $user->enrollments()
            ->where('course_id', $lesson->section->course_id)
            ->where('is_active', true)
            ->first();
            
        if (!$enrollment || $enrollment->isExpired()) {
            Log::debug('StudentProgress.updateProgress forbidden', [
                'has_enrollment' => (bool) $enrollment,
                'enrollment_id' => $enrollment?->id,
                'is_active' => $enrollment?->is_active,
                'is_expired' => $enrollment?->isExpired(),
                'expires_at' => optional($enrollment?->expires_at)->toIso8601String(),
            ]);
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        // Interpreta correttamente il boolean 'completed'
        $completed = $request->has('completed') ? $request->boolean('completed') : false;

        // Aggiorna o crea il progresso
        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $validated['lesson_id'],
            ],
            [
                'watch_time_seconds' => $validated['watch_time_seconds'],
                'progress_percentage' => $validated['progress_percentage'],
                'completed' => $completed,
                'completed_at' => $completed ? now() : null,
            ]
        );
        Log::debug('StudentProgress.updateProgress saved', ['progress_id' => $progress->id]);

        // Aggiorna il progresso generale del corso
        $this->updateCourseProgress($user->id, $lesson->section->course_id);
        Log::debug('StudentProgress.updateProgress courseProgressUpdated', [
            'course_id' => $lesson->section->course_id,
        ]);

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    public function markAsCompleted(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id'
        ]);

        $user = Auth::user();
        $lesson = Lesson::findOrFail($validated['lesson_id']);
        
        // Verifica che l'utente sia iscritto al corso
        $enrollment = $user->enrollments()
            ->where('course_id', $lesson->section->course_id)
            ->where('is_active', true)
            ->first();
            
        if (!$enrollment || $enrollment->isExpired()) {
            Log::debug('StudentProgress.markAsCompleted forbidden', [
                'has_enrollment' => (bool) $enrollment,
                'enrollment_id' => $enrollment?->id,
                'is_active' => $enrollment?->is_active,
                'is_expired' => $enrollment?->isExpired(),
                'expires_at' => optional($enrollment?->expires_at)->toIso8601String(),
            ]);
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        // Marca come completata
        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $validated['lesson_id'],
            ],
            [
                'completed' => true,
                'completed_at' => now(),
                'progress_percentage' => 100,
            ]
        );
        Log::debug('StudentProgress.markAsCompleted saved', ['progress_id' => $progress->id]);

        // Aggiorna il progresso generale del corso
        $this->updateCourseProgress($user->id, $lesson->section->course_id);

        // If this is an AJAX/JSON request, return JSON, otherwise redirect back
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lezione completata!',
                'progress' => $progress
            ]);
        }

        return back()->with('success', 'Lezione completata!');
    }

    private function updateCourseProgress($userId, $courseId)
    {
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
            
        if (!$enrollment) {
            return;
        }

        // Calcola il progresso totale del corso evitando join complesse
        $sectionIds = Section::where('course_id', $courseId)->pluck('id');
        $lessonIds = Lesson::whereIn('section_id', $sectionIds)->pluck('id');
        $totalLessons = $lessonIds->count();
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->where('completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
        
        $enrollment->update(['progress_percentage' => $progressPercentage]);
    }
}

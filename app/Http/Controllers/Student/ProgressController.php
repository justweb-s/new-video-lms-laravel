<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Verifica che l'utente sia iscritto al corso
        $enrollment = $user->enrollments()
            ->where('course_id', $lesson->section->course_id)
            ->where('is_active', true)
            ->first();
            
        if (!$enrollment || $enrollment->isExpired()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        // Aggiorna o crea il progresso
        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $validated['lesson_id'],
            ],
            [
                'watch_time_seconds' => $validated['watch_time_seconds'],
                'progress_percentage' => $validated['progress_percentage'],
                'completed' => $validated['completed'] ?? false,
                'completed_at' => $validated['completed'] ? now() : null,
            ]
        );

        // Aggiorna il progresso generale del corso
        $this->updateCourseProgress($user->id, $lesson->section->course_id);

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

        // Aggiorna il progresso generale del corso
        $this->updateCourseProgress($user->id, $lesson->section->course_id);

        return response()->json([
            'success' => true,
            'message' => 'Lezione completata!',
            'progress' => $progress
        ]);
    }

    private function updateCourseProgress($userId, $courseId)
    {
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
            
        if (!$enrollment) {
            return;
        }

        // Calcola il progresso totale del corso
        $totalLessons = $enrollment->course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $enrollment->course->lessons()->pluck('id'))
            ->where('completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
        
        $enrollment->update(['progress_percentage' => $progressPercentage]);
    }
}

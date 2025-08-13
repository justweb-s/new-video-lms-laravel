<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verifica se l'utente è attivo
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Account disattivato. Contatta il supporto.');
        }

        // Recupera i corsi ai quali lo studente è iscritto
        $enrollments = $user->enrollments()
            ->with(['course.sections.lessons', 'course.workoutCard'])
            ->where('is_active', true)
            ->get();

        $enrolledCourses = [];
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            
            // Calcola il progresso per ogni corso
            $totalLessons = $course->lessons()->count();
            $completedLessons = 0;
            
            if ($totalLessons > 0) {
                $completedLessons = LessonProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $course->lessons()->pluck('lessons.id'))
                    ->where('completed', true)
                    ->count();
            }
            
            $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
            
            // Aggiorna il progresso nell'enrollment
            $enrollment->update(['progress_percentage' => $progressPercentage]);
            
            $enrolledCourses[] = [
                'course' => $course,
                'enrollment' => $enrollment,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'progress_percentage' => $progressPercentage,
                'is_expired' => $enrollment->isExpired(),
                'is_active' => $enrollment->isActive(),
            ];
        }

        // Statistiche generali
        $stats = [
            'total_courses' => count($enrolledCourses),
            'active_courses' => collect($enrolledCourses)->where('is_active', true)->count(),
            'total_lessons_completed' => collect($enrolledCourses)->sum('completed_lessons'),
            'average_progress' => collect($enrolledCourses)->avg('progress_percentage') ?: 0,
        ];

        // Ultime lezioni completate
        $recentProgress = LessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->with(['lesson.section.course'])
            ->latest('completed_at')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('enrolledCourses', 'stats', 'recentProgress'));
    }
}

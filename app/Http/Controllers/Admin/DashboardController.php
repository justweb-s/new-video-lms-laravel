<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_students' => User::count(),
            'active_students' => User::where('is_active', true)->count(),
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::where('is_active', true)->count(),
            'completed_lessons' => LessonProgress::where('completed', true)->count(),
        ];

        // Ultimi studenti registrati
        $recent_students = User::latest()->take(5)->get();

        // Corsi più popolari
        $popular_courses = Course::withCount(['enrollments' => function($query) {
            $query->where('is_active', true);
        }])->orderBy('enrollments_count', 'desc')->take(5)->get();

        // Progressi recenti
        $recent_progress = LessonProgress::with(['user', 'lesson.section.course'])
            ->where('completed', true)
            ->latest('completed_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_students', 'popular_courses', 'recent_progress'));
    }
}

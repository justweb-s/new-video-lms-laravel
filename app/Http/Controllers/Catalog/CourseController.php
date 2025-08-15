<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('catalog.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        if (!$course->is_active) {
            abort(404);
        }

        $isEnrolled = false;
        if (Auth::check()) {
            $user = Auth::user();
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->where('is_active', true)
                ->first();
            $isEnrolled = $enrollment && !$enrollment->isExpired();
        }

        return view('catalog.courses.show', compact('course', 'isEnrolled'));
    }

    public function purchase(Request $request, Course $course)
    {
        $user = $request->user();

        if (!$course->is_active) {
            return redirect()->route('catalog.show', $course)
                ->with('error', 'Il corso non è attualmente disponibile.');
        }

        $existing = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if ($existing && $existing->isActive()) {
            return redirect()->route('courses.show', $course)
                ->with('status', 'Sei già iscritto a questo corso.');
        }

        $expiresAt = null;
        if (!empty($course->duration_weeks)) {
            $expiresAt = now()->addWeeks($course->duration_weeks);
        }

        if (!$existing) {
            $existing = new Enrollment([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
        }

        $existing->enrolled_at = now();
        $existing->expires_at = $expiresAt;
        $existing->is_active = true;
        $existing->progress_percentage = 0;
        $existing->save();

        return redirect()->route('courses.show', $course)
            ->with('status', 'Iscrizione completata con successo!');
    }
}

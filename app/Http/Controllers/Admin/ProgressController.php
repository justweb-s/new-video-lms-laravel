<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use App\Models\Course;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display overall progress statistics
     */
    public function index()
    {
        $stats = [
            'total_students' => User::count(),
            'total_courses' => Course::where('is_active', true)->count(),
            'total_lessons' => Lesson::whereHas('section.course', function($q) {
                $q->where('is_active', true);
            })->where('is_active', true)->count(),
            'total_progress_records' => LessonProgress::count(),
            'completed_lessons' => LessonProgress::where('is_completed', true)->count(),
        ];

        // Recent progress activity
        $recentProgress = LessonProgress::with(['user', 'lesson.section.course'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Top performing students
        $topStudents = User::withCount(['lessonProgress as completed_lessons' => function($q) {
                $q->where('is_completed', true);
            }])
            ->having('completed_lessons', '>', 0)
            ->orderBy('completed_lessons', 'desc')
            ->limit(10)
            ->get();

        return view('admin.progress.index', compact('stats', 'recentProgress', 'topStudents'));
    }

    /**
     * Show progress for a specific course
     */
    public function course(Course $course)
    {
        $course->load(['sections.lessons', 'enrollments.user']);
        
        // Calculate course statistics
        $totalLessons = $course->lessons()->count();
        $enrolledStudents = $course->enrollments()->where('is_active', true)->count();
        
        // Progress by student
        $studentProgress = [];
        foreach ($course->enrollments()->where('is_active', true)->with('user')->get() as $enrollment) {
            $user = $enrollment->user;
            $completedLessons = LessonProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                ->where('is_completed', true)
                ->count();
            
            $studentProgress[] = [
                'user' => $user,
                'enrollment' => $enrollment,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'completion_percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0
            ];
        }

        // Sort by completion percentage
        usort($studentProgress, function($a, $b) {
            return $b['completion_percentage'] <=> $a['completion_percentage'];
        });

        return view('admin.progress.course', compact('course', 'studentProgress', 'totalLessons', 'enrolledStudents'));
    }

    /**
     * Show progress for a specific student
     */
    public function student(User $student)
    {
        $student->load(['enrollments.course.sections.lessons']);
        
        // Get all enrolled courses with progress
        $courseProgress = [];
        foreach ($student->enrollments()->where('is_active', true)->with('course.sections.lessons')->get() as $enrollment) {
            $course = $enrollment->course;
            $totalLessons = $course->lessons()->count();
            $completedLessons = LessonProgress::where('user_id', $student->id)
                ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                ->where('is_completed', true)
                ->count();
            
            $courseProgress[] = [
                'course' => $course,
                'enrollment' => $enrollment,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'completion_percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0
            ];
        }

        // Recent activity
        $recentActivity = LessonProgress::where('user_id', $student->id)
            ->with(['lesson.section.course'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.progress.student', compact('student', 'courseProgress', 'recentActivity'));
    }

    /**
     * Show detailed lesson progress
     */
    public function lesson(Lesson $lesson)
    {
        $lesson->load(['section.course']);
        
        // Get all students enrolled in this lesson's course
        $enrolledStudents = User::whereHas('enrollments', function($q) use ($lesson) {
                $q->where('course_id', $lesson->section->course_id)
                  ->where('is_active', true);
            })
            ->with(['lessonProgress' => function($q) use ($lesson) {
                $q->where('lesson_id', $lesson->id);
            }])
            ->get();

        // Calculate statistics
        $totalStudents = $enrolledStudents->count();
        $studentsWithProgress = $enrolledStudents->filter(function($student) {
            return $student->lessonProgress->isNotEmpty();
        })->count();
        $completedStudents = $enrolledStudents->filter(function($student) {
            return $student->lessonProgress->isNotEmpty() && $student->lessonProgress->first()->is_completed;
        })->count();

        return view('admin.progress.lesson', compact('lesson', 'enrolledStudents', 'totalStudents', 'studentsWithProgress', 'completedStudents'));
    }

    /**
     * Update lesson progress for a student (admin override)
     */
    public function updateLessonProgress(Request $request, User $student, Lesson $lesson)
    {
        $validated = $request->validate([
            'is_completed' => 'required|boolean',
            'watched_duration' => 'nullable|integer|min:0',
            'completion_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $student->id,
                'lesson_id' => $lesson->id
            ],
            array_merge($validated, [
                'last_watched_at' => now()
            ])
        );

        return redirect()->back()->with('success', 'Progresso aggiornato con successo!');
    }

    /**
     * Reset progress for a student in a course
     */
    public function resetCourseProgress(User $student, Course $course)
    {
        $lessonIds = $course->lessons()->pluck('id');
        
        LessonProgress::where('user_id', $student->id)
            ->whereIn('lesson_id', $lessonIds)
            ->delete();

        return redirect()->back()->with('success', 'Progresso del corso resettato con successo!');
    }

    /**
     * Export progress data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'all'); // all, course, student
        $id = $request->get('id');

        $query = LessonProgress::with(['user', 'lesson.section.course']);

        if ($type === 'course' && $id) {
            $course = Course::findOrFail($id);
            $lessonIds = $course->lessons()->pluck('id');
            $query->whereIn('lesson_id', $lessonIds);
        } elseif ($type === 'student' && $id) {
            $query->where('user_id', $id);
        }

        $progressData = $query->get();

        // Return CSV or JSON based on request
        if ($request->get('format') === 'json') {
            return response()->json($progressData);
        }

        // Generate CSV
        $csvData = [];
        $csvData[] = ['Student', 'Course', 'Section', 'Lesson', 'Completed', 'Watched Duration', 'Completion %', 'Last Watched'];
        
        foreach ($progressData as $progress) {
            $csvData[] = [
                $progress->user->name,
                $progress->lesson->section->course->name,
                $progress->lesson->section->name,
                $progress->lesson->title,
                $progress->is_completed ? 'Yes' : 'No',
                $progress->watched_duration ?? 0,
                $progress->completion_percentage ?? 0,
                $progress->last_watched_at ? $progress->last_watched_at->format('Y-m-d H:i:s') : ''
            ];
        }

        $filename = 'progress_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response()->streamDownload(function() use ($csvData) {
            $handle = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}

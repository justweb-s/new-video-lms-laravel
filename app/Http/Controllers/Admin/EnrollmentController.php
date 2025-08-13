<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course']);
        
        // Filtri
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('course', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        
        // Statistiche
        $stats = [
            'total' => Enrollment::count(),
            'active' => Enrollment::where('is_active', true)->count(),
            'inactive' => Enrollment::where('is_active', false)->count(),
            'expired' => Enrollment::where('expires_at', '<', now())->count(),
        ];
        
        return view('admin.enrollments.index', compact('enrollments', 'courses', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $students = User::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.enrollments.create', compact('courses', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrolled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:enrolled_at',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Verifica che non esista già un'iscrizione attiva
        $existingEnrollment = Enrollment::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->where('is_active', true)
            ->first();
            
        if ($existingEnrollment) {
            return back()->withErrors([
                'user_id' => 'Lo studente è già iscritto a questo corso.'
            ])->withInput();
        }

        $validated['enrolled_at'] = $validated['enrolled_at'] ?? now();
        $validated['is_active'] = $request->has('is_active');

        $enrollment = Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Iscrizione creata con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load([
            'user.lessonProgress.lesson.section',
            'course.sections.lessons',
            'course.workoutCard'
        ]);
        
        // Calcola statistiche del progresso
        $totalLessons = $enrollment->course->lessons()->count();
        $completedLessons = $enrollment->user->lessonProgress()
            ->whereHas('lesson.section', function($q) use ($enrollment) {
                $q->where('course_id', $enrollment->course_id);
            })
            ->where('is_completed', true)
            ->count();
            
        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0;
        
        // Attività recente
        $recentActivity = $enrollment->user->lessonProgress()
            ->whereHas('lesson.section', function($q) use ($enrollment) {
                $q->where('course_id', $enrollment->course_id);
            })
            ->with(['lesson.section'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.enrollments.show', compact(
            'enrollment', 
            'totalLessons', 
            'completedLessons', 
            'progressPercentage', 
            'recentActivity'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment)
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $students = User::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.enrollments.edit', compact('enrollment', 'courses', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'enrolled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:enrolled_at',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Verifica che non esista già un'altra iscrizione attiva per lo stesso utente/corso
        if ($validated['user_id'] != $enrollment->user_id || $validated['course_id'] != $enrollment->course_id) {
            $existingEnrollment = Enrollment::where('user_id', $validated['user_id'])
                ->where('course_id', $validated['course_id'])
                ->where('is_active', true)
                ->where('id', '!=', $enrollment->id)
                ->first();
                
            if ($existingEnrollment) {
                return back()->withErrors([
                    'user_id' => 'Lo studente è già iscritto a questo corso.'
                ])->withInput();
            }
        }

        $validated['is_active'] = $request->has('is_active');

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.show', $enrollment)
            ->with('success', 'Iscrizione aggiornata con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        // Elimina anche tutti i progressi delle lezioni associate
        $lessonIds = $enrollment->course->lessons()->pluck('id');
        $enrollment->user->lessonProgress()
            ->whereIn('lesson_id', $lessonIds)
            ->delete();
            
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Iscrizione eliminata con successo!');
    }
    
    /**
     * Bulk operations for enrollments
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id'
        ]);
        
        $enrollments = Enrollment::whereIn('id', $validated['enrollment_ids']);
        
        switch ($validated['action']) {
            case 'activate':
                $enrollments->update(['is_active' => true]);
                $message = 'Iscrizioni attivate con successo!';
                break;
                
            case 'deactivate':
                $enrollments->update(['is_active' => false]);
                $message = 'Iscrizioni disattivate con successo!';
                break;
                
            case 'delete':
                // Elimina anche i progressi associati
                foreach ($enrollments->with('course.lessons')->get() as $enrollment) {
                    $lessonIds = $enrollment->course->lessons->pluck('id');
                    $enrollment->user->lessonProgress()
                        ->whereIn('lesson_id', $lessonIds)
                        ->delete();
                }
                $enrollments->delete();
                $message = 'Iscrizioni eliminate con successo!';
                break;
        }
        
        return redirect()->route('admin.enrollments.index')
            ->with('success', $message);
    }
    
    /**
     * Export enrollments data
     */
    public function export(Request $request)
    {
        $query = Enrollment::with(['user', 'course']);
        
        // Apply same filters as index
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }
        
        $enrollments = $query->get();
        
        $csvData = [];
        $csvData[] = ['Student Name', 'Student Email', 'Course', 'Enrolled At', 'Expires At', 'Status', 'Notes'];
        
        foreach ($enrollments as $enrollment) {
            $csvData[] = [
                $enrollment->user->name,
                $enrollment->user->email,
                $enrollment->course->name,
                $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : '',
                $enrollment->expires_at ? $enrollment->expires_at->format('Y-m-d H:i:s') : '',
                $enrollment->is_active ? 'Active' : 'Inactive',
                $enrollment->notes ?? ''
            ];
        }
        
        $filename = 'enrollments_export_' . date('Y-m-d_H-i-s') . '.csv';
        
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

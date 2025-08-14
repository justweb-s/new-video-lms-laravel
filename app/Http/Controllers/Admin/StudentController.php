<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = User::withCount('enrollments')->latest()->paginate(15);
        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();

        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        // Ensure is_active is set properly
        $validated['is_active'] = $request->has('is_active');
        
        // Set email_verified_at
        $validated['email_verified_at'] = now();

        $student = User::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Studente creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $student)
    {
        $student->load(['enrollments.course', 'lessonProgress.lesson.section']);
        $student->loadCount(['enrollments']);
        
        // Get recent progress
        $recentProgress = $student->lessonProgress()
            ->with(['lesson.section.course'])
            ->latest()
            ->take(10)
            ->get();
            
        return view('admin.students.show', compact('student', 'recentProgress'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $student)
    {
        $student->loadCount(['enrollments']);
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, User $student)
    {
        $validated = $request->validated();

        // Hash password only if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Ensure is_active is set properly
        $validated['is_active'] = $request->has('is_active');

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Studente aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $student)
    {
        // Delete the student (cascade will handle related records)
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Studente eliminato con successo!');
    }

    /**
     * Show the enrollments management page for a student.
     */
    public function enrollments(User $student)
    {
        $student->load(['enrollments.course']);
        
        // Get courses that the student is not enrolled in
        $enrolledCourseIds = $student->enrollments->pluck('course_id');
        $availableCourses = Course::where('is_active', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->get();
            
        return view('admin.students.enrollments', compact('student', 'availableCourses'));
    }

    /**
     * Store a new enrollment for a student.
     */
    public function storeEnrollment(Request $request, User $student)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'expires_at' => 'nullable|date|after:today'
        ]);

        // Check if student is already enrolled in this course
        $existingEnrollment = $student->enrollments()->where('course_id', $validated['course_id'])->first();
        if ($existingEnrollment) {
            return back()->with('error', 'Lo studente è già iscritto a questo corso.');
        }

        $enrollment = new Enrollment([
            'user_id' => $student->id,
            'course_id' => $validated['course_id'],
            'enrolled_at' => now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
            'progress_percentage' => 0
        ]);
        
        $enrollment->save();

        return back()->with('success', 'Iscrizione aggiunta con successo!');
    }

    /**
     * Toggle enrollment status.
     */
    public function toggleEnrollment(User $student, Enrollment $enrollment)
    {
        $enrollment->update([
            'is_active' => !$enrollment->is_active
        ]);

        $status = $enrollment->is_active ? 'attivata' : 'disattivata';
        return back()->with('success', "Iscrizione {$status} con successo!");
    }

    /**
     * Delete an enrollment.
     */
    public function destroyEnrollment(User $student, Enrollment $enrollment)
    {
        $enrollment->delete();
        return back()->with('success', 'Iscrizione eliminata con successo!');
    }
}

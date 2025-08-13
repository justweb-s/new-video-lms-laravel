<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::withCount(['sections', 'enrollments'])->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = $imagePath;
        }

        // Ensure is_active is set properly
        $validated['is_active'] = $request->has('is_active');

        $course = Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['sections.lessons', 'enrollments.user', 'workoutCard']);
        $course->loadCount(['sections', 'enrollments']);
        $course->total_lessons = $course->sections->sum(function ($section) {
            return $section->lessons->count();
        });
        
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $course->loadCount(['sections', 'enrollments']);
        $course->total_lessons = $course->sections->sum(function ($section) {
            return $section->lessons->count();
        });
        
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image_url && Storage::disk('public')->exists($course->image_url)) {
                Storage::disk('public')->delete($course->image_url);
            }
            $imagePath = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = $imagePath;
        }

        // Ensure is_active is set properly
        $validated['is_active'] = $request->has('is_active');

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Corso aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Delete associated image if exists
        if ($course->image_url && Storage::disk('public')->exists($course->image_url)) {
            Storage::disk('public')->delete($course->image_url);
        }

        // Delete the course (cascade will handle related records)
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso eliminato con successo!');
    }
}

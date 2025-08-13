<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Course;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $course->load(['sections.lessons']);
        $course->loadCount(['sections', 'lessons']);
        
        return view('admin.courses.sections.index', compact('course'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        return view('admin.courses.sections.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'section_order' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_active'] = $request->has('is_active');

        $section = Section::create($validated);

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', 'Sezione creata con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Section $section)
    {
        $section->load(['lessons', 'course']);
        $section->loadCount(['lessons']);
        
        return view('admin.courses.sections.show', compact('course', 'section'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Section $section)
    {
        return view('admin.courses.sections.edit', compact('course', 'section'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'section_order' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $section->update($validated);

        return redirect()->route('admin.courses.sections.show', [$course, $section])
            ->with('success', 'Sezione aggiornata con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Section $section)
    {
        $section->delete();

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', 'Sezione eliminata con successo!');
    }
}

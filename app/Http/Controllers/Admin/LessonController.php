<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Section $section)
    {
        $section->load(['lessons', 'course']);
        $section->loadCount(['lessons']);
        
        return view('admin.courses.sections.lessons.index', compact('course', 'section'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course, Section $section)
    {
        return view('admin.courses.sections.lessons.create', compact('course', 'section'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course, Section $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video_url' => 'required|url',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'lesson_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'video_metadata' => 'nullable|json'
        ]);

        $validated['section_id'] = $section->id;
        $validated['is_active'] = $request->has('is_active');

        $lesson = Lesson::create($validated);

        return redirect()->route('admin.courses.sections.lessons.index', [$course, $section])
            ->with('success', 'Lezione creata con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Section $section, Lesson $lesson)
    {
        $lesson->load(['section.course', 'progress']);
        $lesson->loadCount(['progress']);
        
        return view('admin.courses.sections.lessons.show', compact('course', 'section', 'lesson'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Section $section, Lesson $lesson)
    {
        return view('admin.courses.sections.lessons.edit', compact('course', 'section', 'lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course, Section $section, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'video_url' => 'required|url',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'lesson_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'video_metadata' => 'nullable|json'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $lesson->update($validated);

        return redirect()->route('admin.courses.sections.lessons.show', [$course, $section, $lesson])
            ->with('success', 'Lezione aggiornata con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Section $section, Lesson $lesson)
    {
        $lesson->delete();

        return redirect()->route('admin.courses.sections.lessons.index', [$course, $section])
            ->with('success', 'Lezione eliminata con successo!');
    }
}

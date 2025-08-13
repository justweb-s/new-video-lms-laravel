<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkoutCard;
use App\Models\Course;
use Illuminate\Http\Request;

class WorkoutCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workoutCards = WorkoutCard::with(['course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.workout-cards.index', compact('workoutCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::where('is_active', true)
            ->whereDoesntHave('workoutCard')
            ->orderBy('name')
            ->get();
        
        return view('admin.workout-cards.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id|unique:workout_cards,course_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'pdf_url' => 'nullable|url',
            'exercises' => 'nullable|json',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration' => 'nullable|integer|min:1|max:300',
            'equipment_needed' => 'nullable|string|max:1000',
            'instructions' => 'nullable|string|max:5000',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $workoutCard = WorkoutCard::create($validated);

        return redirect()->route('admin.workout-cards.index')
            ->with('success', 'Scheda di allenamento creata con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkoutCard $workoutCard)
    {
        $workoutCard->load(['course.enrollments.user']);
        
        return view('admin.workout-cards.show', compact('workoutCard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkoutCard $workoutCard)
    {
        $courses = Course::where('is_active', true)
            ->where(function($query) use ($workoutCard) {
                $query->whereDoesntHave('workoutCard')
                      ->orWhere('id', $workoutCard->course_id);
            })
            ->orderBy('name')
            ->get();
        
        return view('admin.workout-cards.edit', compact('workoutCard', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkoutCard $workoutCard)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id|unique:workout_cards,course_id,' . $workoutCard->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'pdf_url' => 'nullable|url',
            'exercises' => 'nullable|json',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration' => 'nullable|integer|min:1|max:300',
            'equipment_needed' => 'nullable|string|max:1000',
            'instructions' => 'nullable|string|max:5000',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $workoutCard->update($validated);

        return redirect()->route('admin.workout-cards.show', $workoutCard)
            ->with('success', 'Scheda di allenamento aggiornata con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkoutCard $workoutCard)
    {
        $workoutCard->delete();

        return redirect()->route('admin.workout-cards.index')
            ->with('success', 'Scheda di allenamento eliminata con successo!');
    }
}

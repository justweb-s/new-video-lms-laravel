<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'video_file' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg|max:1024000', // Max 1GB
            'duration_minutes' => 'required|integer|min:1|max:600',
            'lesson_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'video_metadata' => 'nullable|json'
        ]);

        if ($request->hasFile('video_file')) {
            // Carica su S3 mantenendo il nome originale (sanitizzato) e prevenendo collisioni
            $uploadedFile = $request->file('video_file');
            $originalName = $uploadedFile->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $safeBase = Str::slug($baseName) ?: 'video';
            $fileName = $safeBase . '.' . $extension;
            $key = 'videos/' . $fileName;
            $counter = 1;
            while (Storage::disk('s3')->exists($key)) {
                $fileName = $safeBase . '-' . $counter . '.' . $extension;
                $key = 'videos/' . $fileName;
                $counter++;
            }
            $uploadedFile->storeAs('videos', $fileName, 's3');
            $validated['video_url'] = Storage::disk('s3')->url($key);
        }

        $validated['section_id'] = $section->id;
        $validated['is_active'] = $request->has('is_active');

        $lesson = Lesson::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'redirect' => route('admin.courses.sections.lessons.index', [$course, $section])
            ]);
        }

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
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg|max:1024000', // Max 1GB
            'duration_minutes' => 'required|integer|min:1|max:600',
            'lesson_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'video_metadata' => 'nullable|json'
        ]);

        if ($request->hasFile('video_file')) {
            // Delete old video if it exists
            if ($lesson->video_url) {
                $oldKey = ltrim(parse_url($lesson->video_url, PHP_URL_PATH), '/');
                $bucket = config('filesystems.disks.s3.bucket');
                // Se l'URL è del tipo <bucket>.s3.<region>.amazonaws.com/<key>
                if ($bucket && (str_starts_with($oldKey, $bucket . '/') || Str::startsWith($oldKey, $bucket . '/'))) {
                    $oldKey = substr($oldKey, strlen($bucket) + 1);
                }
                
                if (!empty($oldKey)) {
                    Storage::disk('s3')->delete($oldKey);
                }
            }

            // Carica su S3 mantenendo il nome originale (sanitizzato) e prevenendo collisioni
            $uploadedFile = $request->file('video_file');
            $originalName = $uploadedFile->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $safeBase = Str::slug($baseName) ?: 'video';
            $fileName = $safeBase . '.' . $extension;
            $key = 'videos/' . $fileName;
            $counter = 1;
            while (Storage::disk('s3')->exists($key)) {
                $fileName = $safeBase . '-' . $counter . '.' . $extension;
                $key = 'videos/' . $fileName;
                $counter++;
            }
            $uploadedFile->storeAs('videos', $fileName, 's3');
            $validated['video_url'] = Storage::disk('s3')->url($key);
        }

        $validated['is_active'] = $request->has('is_active');

        $lesson->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'redirect' => route('admin.courses.sections.lessons.show', [$course, $section, $lesson])
            ]);
        }

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

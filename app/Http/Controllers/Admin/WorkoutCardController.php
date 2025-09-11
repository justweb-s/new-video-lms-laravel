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
     * Show the visual builder for creating/editing workout cards.
     */
    public function builder(Course $course = null, WorkoutCard $workoutCard = null)
    {
        // Se non è specificato un corso, prendi dalla workout card esistente
        if (!$course && $workoutCard) {
            $course = $workoutCard->course;
        }
        
        // Se non c'è né corso né workout card, redirect all'index
        if (!$course) {
            return redirect()->route('admin.workout-cards.index')
                ->with('error', 'Corso non specificato.');
        }

        // Se non è stata passata una workout card, cerca se esiste già per questo corso
        if (!$workoutCard) {
            $workoutCard = WorkoutCard::where('course_id', $course->id)->first();
        }

        // Dati di default per il builder
        $data = [
            'header_logo_url' => 'https://www.emyworkout.it/wp-content/uploads/2024/10/EMY-WORKOUT-%E2%80%A2-Loghi-Finali_LOGO1-.svg',
            'info_box_scadenza' => '1 mese dalla ricezione',
            'info_box_check' => '3 foto (frontale-laterale-posteriore) a inizio e fine mese.',
            'workouts' => []
        ];

        $cardTitle = 'Scheda ' . $course->name;

        // Se stiamo modificando una workout card esistente, parsa i dati
        if ($workoutCard && $workoutCard->content) {
            $parsedData = $this->parseHtmlContent($workoutCard->content);
            if ($parsedData) {
                $data = array_merge($data, $parsedData);
            }
            $cardTitle = $workoutCard->title;
        }

        return view('admin.workout-cards.builder', compact('course', 'workoutCard', 'data', 'cardTitle'));
    }

    /**
     * Store workout card from builder.
     */
    public function storeFromBuilder(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'card_title' => 'required|string|max:255',
            'header_logo_url' => 'nullable|url',
            'info_box_scadenza' => 'nullable|string',
            'info_box_check' => 'nullable|string',
            'workouts' => 'nullable|array',
            'workouts.*.title' => 'nullable|string',
            'workouts.*.warmup' => 'nullable|string',
            'workouts.*.venous_return' => 'nullable|string',
            'workouts.*.exercises' => 'nullable|array',
            'workouts.*.exercises.*.name' => 'nullable|string',
            'workouts.*.exercises.*.series' => 'nullable|string',
            'workouts.*.exercises.*.reps' => 'nullable|string',
            'workouts.*.exercises.*.rest' => 'nullable|string',
            'workouts.*.exercises.*.note' => 'nullable|string',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $content = $this->buildHtmlFromData($validated, $course->name);
        
        // Estrai warmup e venous_return per i campi separati
        $warmupValues = [];
        $venousReturnValues = [];
        if (isset($validated['workouts'])) {
            foreach ($validated['workouts'] as $workout) {
                if (!empty($workout['warmup'])) {
                    $warmupValues[] = $workout['warmup'];
                }
                if (!empty($workout['venous_return'])) {
                    $venousReturnValues[] = $workout['venous_return'];
                }
            }
        }
        $warmupContent = implode("\n---\n", $warmupValues);
        $venousReturnContent = implode("\n---\n", $venousReturnValues);

        // Controlla se esiste già una workout card per questo corso
        $workoutCard = WorkoutCard::where('course_id', $validated['course_id'])->first();
        
        if ($workoutCard) {
            $workoutCard->update([
                'title' => $validated['card_title'],
                'content' => $content,
                'warmup' => $warmupContent,
                'venous_return' => $venousReturnContent,
                'is_active' => true,
            ]);
        } else {
            $workoutCard = WorkoutCard::create([
                'course_id' => $validated['course_id'],
                'title' => $validated['card_title'],
                'content' => $content,
                'warmup' => $warmupContent,
                'venous_return' => $venousReturnContent,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.workout-cards.builder', $course)
            ->with('success', 'Scheda di allenamento salvata con successo!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id|unique:workout_cards,course_id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'warmup' => 'nullable|string',
            'venous_return' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Sanitize manual HTML content to prevent XSS if edited via textarea
        $validated['content'] = $this->sanitizeHtml($validated['content']);

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
            'content' => 'required|string',
            'warmup' => 'nullable|string',
            'venous_return' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Sanitize manual HTML content to prevent XSS if edited via textarea
        $validated['content'] = $this->sanitizeHtml($validated['content']);

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

    /**
     * Build HTML content from form data.
     */
    private function buildHtmlFromData(array $data, string $courseName): string
    {
        $headerLogoUrl = $data['header_logo_url'] ?? '';
        $infoBoxScadenza = $data['info_box_scadenza'] ?? '';
        $infoBoxCheck = $data['info_box_check'] ?? '';

        $html = "<div class='card-header'>\n";
        $html .= "    <img src='" . htmlspecialchars($headerLogoUrl) . "' alt='Logo' class='logo'>\n";
        $html .= "    <h1>" . htmlspecialchars(strtoupper($courseName)) . "</h1>\n";
        $html .= "</div>\n";

        $html .= "<div class='info-box'>\n";
        $html .= "    <p><strong>SCADENZA PROGRAMMA:</strong> " . htmlspecialchars($infoBoxScadenza) . "</p>\n";
        $html .= "    <p><strong>CHECK:</strong> " . htmlspecialchars($infoBoxCheck) . "</p>\n";
        $html .= "</div>\n";

        if (isset($data['workouts'])) {
            foreach ($data['workouts'] as $index => $workout) {
                $workoutTitle = $workout['title'] ?? 'WORKOUT ' . ($index + 1);
                $warmup = $workout['warmup'] ?? '';
                $venousReturn = $workout['venous_return'] ?? '';

                $html .= "<section class='workout-section'>\n";
                $html .= "    <h2 class='workout-title'>" . htmlspecialchars($workoutTitle) . "</h2>\n";
                $html .= "    <div class='warmup'><strong>RISCALDAMENTO:</strong> " . nl2br(htmlspecialchars($warmup)) . "</div>\n";
                $html .= "    <table>\n<thead><tr><th>ESERCIZIO</th><th>SERIE</th><th>RIPETIZIONI</th><th>REST E T.U.T</th><th>NOTE</th></tr></thead>\n<tbody>\n";

                if (isset($workout['exercises'])) {
                    foreach ($workout['exercises'] as $exercise) {
                        $html .= "<tr>\n";
                        $html .= "    <td data-label='ESERCIZIO'>" . htmlspecialchars($exercise['name'] ?? '') . "</td>\n";
                        $html .= "    <td data-label='SERIE'>" . htmlspecialchars($exercise['series'] ?? '') . "</td>\n";
                        $html .= "    <td data-label='RIPETIZIONI'>" . htmlspecialchars($exercise['reps'] ?? '') . "</td>\n";
                        $html .= "    <td data-label='REST E T.U.T'>" . htmlspecialchars($exercise['rest'] ?? '') . "</td>\n";
                        $html .= "    <td data-label='NOTE'>" . htmlspecialchars($exercise['note'] ?? '') . "</td>\n";
                        $html .= "</tr>\n";
                    }
                }

                $html .= "</tbody>\n</table>\n";
                $html .= "    <div class='venous-return'><strong>5' RITORNO VENOSO</strong><br>" . nl2br(htmlspecialchars($venousReturn)) . "</div>\n";
                $html .= "</section>\n";
            }
        }

        return $html;
    }

    /**
     * Sanitize manual HTML content to allow only whitelisted tags/attributes.
     */
    private function sanitizeHtml(string $html): string
    {
        // Prefer package sanitizer if installed
        if (class_exists(\Mews\Purifier\Facades\Purifier::class)) {
            try {
                return \Mews\Purifier\Facades\Purifier::clean($html, 'workout');
            } catch (\Throwable $e) {
                // fall through to DOM-based sanitizer
            }
        }

        // Fallback: allow only tags used by the builder output
        $allowedTags = '<div><img><h1><h2><p><strong><table><thead><tbody><tr><th><td><br><section>';
        $stripped = strip_tags($html, $allowedTags);

        $doc = new \DOMDocument();
        @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $stripped);

        // Remove potentially dangerous nodes altogether
        foreach (['script','style','iframe','object','embed'] as $tag) {
            while (($nodes = $doc->getElementsByTagName($tag))->length) {
                $node = $nodes->item(0);
                $node->parentNode?->removeChild($node);
            }
        }

        $allowedAttrs = ['class','src','alt','data-label'];
        $all = $doc->getElementsByTagName('*');
        for ($i = $all->length - 1; $i >= 0; $i--) {
            $el = $all->item($i);
            if ($el->hasAttributes()) {
                $toRemove = [];
                foreach (iterator_to_array($el->attributes) as $attr) {
                    $name = $attr->nodeName;
                    if (str_starts_with($name, 'on') || !in_array($name, $allowedAttrs, true)) {
                        $toRemove[] = $name;
                    }
                }
                foreach ($toRemove as $name) {
                    $el->removeAttribute($name);
                }
            }
            if ($el->tagName === 'img') {
                $src = $el->getAttribute('src');
                if (!preg_match('#^https?://#i', $src)) {
                    $el->removeAttribute('src');
                }
            }
        }

        // Extract inner HTML from body
        $body = $doc->getElementsByTagName('body')->item(0);
        $result = '';
        if ($body) {
            foreach ($body->childNodes as $child) {
                $result .= $doc->saveHTML($child);
            }
        }

        return $result;
    }

    /**
     * Parse HTML content to extract structured data.
     */
    private function parseHtmlContent(string $htmlContent): ?array
    {
        try {
            $doc = new \DOMDocument();
            @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent);
            $xpath = new \DOMXPath($doc);

            $data = [];

            // Estrai dati intestazione
            $logoImg = $xpath->query("//div[@class='card-header']/img")->item(0);
            $data['header_logo_url'] = $logoImg ? $logoImg->getAttribute('src') : '';

            $infoBoxP = $xpath->query("//div[@class='info-box']/p");
            if ($infoBoxP->length >= 2) {
                $data['info_box_scadenza'] = trim(str_replace('SCADENZA PROGRAMMA:', '', $infoBoxP->item(0)->nodeValue ?? ''));
                $data['info_box_check'] = trim(str_replace('CHECK:', '', $infoBoxP->item(1)->nodeValue ?? ''));
            }

            $data['workouts'] = [];
            $workoutSections = $xpath->query('//section[contains(@class, "workout-section")]');
            
            foreach ($workoutSections as $i => $section) {
                $workoutData = [];
                $workoutData['title'] = $xpath->evaluate('string(.//h2)', $section);
                
                $warmupText = $xpath->evaluate('string(.//div[contains(@class, "warmup")])', $section);
                $workoutData['warmup'] = trim(str_replace(['RISCALDAMENTO:', '<strong>RISCALDAMENTO:</strong>'], '', $warmupText));

                $venousReturnText = $xpath->evaluate('string(.//div[contains(@class, "venous-return")])', $section);
                $workoutData['venous_return'] = trim(str_replace(["5' RITORNO VENOSO", "<strong>5' RITORNO VENOSO</strong>", "<br>", "<br/>"], '', $venousReturnText));
                
                $workoutData['exercises'] = [];
                $rows = $xpath->query('.//tbody/tr', $section);
                
                foreach ($rows as $row) {
                    $exercise = [];
                    $exercise['name'] = $xpath->evaluate('string(./td[1])', $row);
                    $exercise['series'] = $xpath->evaluate('string(./td[2])', $row);
                    $exercise['reps'] = $xpath->evaluate('string(./td[3])', $row);
                    $exercise['rest'] = $xpath->evaluate('string(./td[4])', $row);
                    $exercise['note'] = $xpath->evaluate('string(./td[5])', $row);
                    $workoutData['exercises'][] = $exercise;
                }
                
                $data['workouts'][] = $workoutData;
            }

            return $data;
        } catch (\Exception $e) {
            // Se il parsing fallisce, ritorna null
            return null;
        }
    }
}

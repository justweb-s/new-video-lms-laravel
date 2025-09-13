<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportExportController extends Controller
{
    public function index()
    {
        return view('admin.data.index');
    }

    // =========================
    // EXPORT
    // =========================

    public function exportCourses(Request $request)
    {
        $format = $request->get('format', 'json'); // only json for nested structures
        $courses = Course::with(['sections.lessons'])->get();

        if ($format === 'json') {
            $payload = [
                'exported_at' => now()->toIso8601String(),
                'count' => $courses->count(),
                'courses' => $courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'description' => $course->description,
                        'image_url' => $course->image_url,
                        'price' => $course->price,
                        'is_active' => (bool) $course->is_active,
                        'duration_days' => $course->duration_days,
                        'prerequisites' => $course->prerequisites,
                        'meta_title' => $course->meta_title,
                        'meta_description' => $course->meta_description,
                        'is_featured' => (bool) $course->is_featured,
                        'sections' => $course->sections->map(function ($section) {
                            return [
                                'id' => $section->id,
                                'name' => $section->name,
                                'description' => $section->description,
                                'section_order' => $section->section_order,
                                'is_active' => (bool) $section->is_active,
                                'lessons' => $section->lessons->map(function ($lesson) {
                                    return [
                                        'id' => $lesson->id,
                                        'title' => $lesson->title,
                                        'description' => $lesson->description,
                                        'video_url' => $lesson->video_url,
                                        'duration_minutes' => $lesson->duration_minutes,
                                        'lesson_order' => $lesson->lesson_order,
                                        'is_active' => (bool) $lesson->is_active,
                                        'video_metadata' => $lesson->video_metadata,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ];

            $filename = 'courses_export_' . date('Y-m-d_H-i-s') . '.json';
            return response()->streamDownload(function () use ($payload) {
                echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }, $filename, [
                'Content-Type' => 'application/json',
            ]);
        }

        return back()->with('error', 'Formato non supportato per l\'export dei corsi. Usa JSON.');
    }

    public function exportStudents(Request $request)
    {
        $students = User::orderBy('id')->get();

        $csv = [];
        $csv[] = [
            'id', 'name', 'first_name', 'last_name', 'email', 'phone', 'is_active', 'last_login',
            'tax_code', 'tax_id', 'billing_address_line1', 'billing_address_line2',
            'billing_city', 'billing_state', 'billing_postal_code', 'billing_country', 'created_at', 'updated_at'
        ];

        foreach ($students as $u) {
            $csv[] = [
                $u->id,
                $u->name,
                $u->first_name,
                $u->last_name,
                $u->email,
                $u->phone,
                $u->is_active ? 1 : 0,
                $u->last_login ? $u->last_login->format('Y-m-d H:i:s') : '',
                $u->tax_code,
                $u->tax_id,
                $u->billing_address_line1,
                $u->billing_address_line2,
                $u->billing_city,
                $u->billing_state,
                $u->billing_postal_code,
                $u->billing_country,
                $u->created_at ? $u->created_at->format('Y-m-d H:i:s') : '',
                $u->updated_at ? $u->updated_at->format('Y-m-d H:i:s') : '',
            ];
        }

        $filename = 'students_export_' . date('Y-m-d_H-i-s') . '.csv';
        return $this->streamCsv($csv, $filename);
    }

    public function exportEnrollments(Request $request)
    {
        $enrollments = Enrollment::with(['user', 'course'])->orderBy('id')->get();

        $csv = [];
        $csv[] = ['user_email', 'course_id', 'course_name', 'enrolled_at', 'expires_at', 'is_active', 'progress_percentage'];

        foreach ($enrollments as $e) {
            $csv[] = [
                $e->user ? $e->user->email : '',
                $e->course_id,
                $e->course ? $e->course->name : '',
                $e->enrolled_at ? $e->enrolled_at->format('Y-m-d') : '',
                $e->expires_at ? $e->expires_at->format('Y-m-d') : '',
                $e->is_active ? 1 : 0,
                $e->progress_percentage ?? 0,
            ];
        }

        $filename = 'enrollments_export_' . date('Y-m-d_H-i-s') . '.csv';
        return $this->streamCsv($csv, $filename);
    }

    public function exportPayments(Request $request)
    {
        $payments = Payment::with(['user', 'course'])->orderBy('id')->get();

        $csv = [];
        $csv[] = [
            'id', 'user_email', 'course_id', 'course_name', 'provider', 'amount_total', 'currency', 'status',
            'stripe_session_id', 'stripe_payment_intent_id', 'paypal_order_id', 'paypal_capture_id',
            'customer_email', 'created_at'
        ];

        foreach ($payments as $p) {
            $csv[] = [
                $p->id,
                $p->user ? $p->user->email : '',
                $p->course_id,
                $p->course ? $p->course->name : '',
                $p->provider,
                $p->amount_total,
                $p->currency,
                $p->status,
                $p->stripe_session_id,
                $p->stripe_payment_intent_id,
                $p->paypal_order_id,
                $p->paypal_capture_id,
                $p->customer_email,
                $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : '',
            ];
        }

        $filename = 'payments_export_' . date('Y-m-d_H-i-s') . '.csv';
        return $this->streamCsv($csv, $filename);
    }

    // =========================
    // IMPORT
    // =========================

    public function importCourses(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:application/json,text/plain,application/octet-stream',
            'replace_children' => 'nullable|boolean',
        ]);

        $replaceChildren = (bool) $request->boolean('replace_children');
        $content = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($content, true);
        if (!$data || !isset($data['courses']) || !is_array($data['courses'])) {
            return back()->with('error', 'File JSON non valido: chiave "courses" mancante o struttura errata.');
        }

        $created = 0; $updated = 0; $sectionsCreated = 0; $lessonsCreated = 0;

        DB::transaction(function () use ($data, $replaceChildren, &$created, &$updated, &$sectionsCreated, &$lessonsCreated) {
            foreach ($data['courses'] as $c) {
                $courseAttrs = [
                    'name' => $c['name'] ?? null,
                    'description' => $c['description'] ?? null,
                    'image_url' => $c['image_url'] ?? null,
                    'price' => $c['price'] ?? 0,
                    'is_active' => (bool)($c['is_active'] ?? true),
                    'duration_days' => $c['duration_days'] ?? null,
                    'prerequisites' => $c['prerequisites'] ?? null,
                    'meta_title' => $c['meta_title'] ?? null,
                    'meta_description' => $c['meta_description'] ?? null,
                    'is_featured' => (bool)($c['is_featured'] ?? false),
                ];

                $course = null;
                if (!empty($c['id'])) {
                    $course = Course::find($c['id']);
                }
                if (!$course && !empty($c['name'])) {
                    $course = Course::where('name', $c['name'])->first();
                }

                if ($course) {
                    $course->update($courseAttrs);
                    $updated++;
                } else {
                    $course = Course::create($courseAttrs);
                    $created++;
                }

                if ($replaceChildren) {
                    // Remove existing children before import
                    foreach ($course->sections as $sec) {
                        $sec->lessons()->delete();
                    }
                    $course->sections()->delete();
                }

                if (!empty($c['sections']) && is_array($c['sections'])) {
                    foreach ($c['sections'] as $s) {
                        $sectionAttrs = [
                            'name' => $s['name'] ?? 'Sezione',
                            'description' => $s['description'] ?? null,
                            'section_order' => $s['section_order'] ?? 0,
                            'is_active' => (bool)($s['is_active'] ?? true),
                        ];

                        $section = null;
                        if (!empty($s['id'])) {
                            $section = Section::where('id', $s['id'])->where('course_id', $course->id)->first();
                        }

                        if ($section) {
                            $section->update($sectionAttrs);
                        } else {
                            $section = $course->sections()->create($sectionAttrs);
                            $sectionsCreated++;
                        }

                        if ($replaceChildren) {
                            $section->lessons()->delete();
                        }

                        if (!empty($s['lessons']) && is_array($s['lessons'])) {
                            foreach ($s['lessons'] as $l) {
                                $lessonAttrs = [
                                    'title' => $l['title'] ?? 'Lezione',
                                    'description' => $l['description'] ?? null,
                                    'video_url' => $l['video_url'] ?? null,
                                    'duration_minutes' => $l['duration_minutes'] ?? null,
                                    'lesson_order' => $l['lesson_order'] ?? 0,
                                    'is_active' => (bool)($l['is_active'] ?? true),
                                    'video_metadata' => $l['video_metadata'] ?? null,
                                ];

                                $lesson = null;
                                if (!empty($l['id'])) {
                                    $lesson = Lesson::where('id', $l['id'])->where('section_id', $section->id)->first();
                                }

                                if ($lesson) {
                                    $lesson->update($lessonAttrs);
                                } else {
                                    $section->lessons()->create($lessonAttrs);
                                    $lessonsCreated++;
                                }
                            }
                        }
                    }
                }
            }
        });

        return back()->with('success', "Import corsi completato. Corsi creati: {$created}, aggiornati: {$updated}. Sezioni create: {$sectionsCreated}, lezioni create: {$lessonsCreated}.");
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/octet-stream,application/csv',
            'default_password' => 'nullable|string|min:6'
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Impossibile leggere il file CSV.');
        }

        $header = null; $count = 0; $created = 0; $updated = 0;
        DB::transaction(function () use ($handle, &$header, &$count, &$created, &$updated, $request) {
            while (($row = fgetcsv($handle)) !== false) {
                if (!$header) {
                    $header = array_map(fn($h) => strtolower(trim($h)), $row);
                    continue;
                }
                $count++;
                $data = $this->combineRow($header, $row);
                if (empty($data['email'])) {
                    continue; // skip invalid row
                }

                $attrs = [
                    'name' => $data['name'] ?? ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''),
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'is_active' => $this->toBool($data['is_active'] ?? 1),
                    'tax_code' => $data['tax_code'] ?? null,
                    'tax_id' => $data['tax_id'] ?? null,
                    'billing_address_line1' => $data['billing_address_line1'] ?? null,
                    'billing_address_line2' => $data['billing_address_line2'] ?? null,
                    'billing_city' => $data['billing_city'] ?? null,
                    'billing_state' => $data['billing_state'] ?? null,
                    'billing_postal_code' => $data['billing_postal_code'] ?? null,
                    'billing_country' => $data['billing_country'] ?? null,
                ];

                $user = User::where('email', $data['email'])->first();
                if ($user) {
                    $user->update($attrs);
                    $updated++;
                } else {
                    $password = $data['password'] ?? $request->input('default_password') ?? Str::random(10);
                    $attrs['email'] = $data['email'];
                    $attrs['password'] = Hash::make($password);
                    $user = User::create($attrs);
                    // Set email verification timestamp even if not fillable
                    $user->forceFill(['email_verified_at' => now()])->save();
                    $created++;
                }
            }
        });
        fclose($handle);

        return back()->with('success', "Import studenti completato. Righe lette: {$count}, creati: {$created}, aggiornati: {$updated}.");
    }

    public function importEnrollments(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/octet-stream,application/csv',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Impossibile leggere il file CSV.');
        }

        $header = null; $count = 0; $created = 0; $updated = 0; $skipped = 0;
        DB::transaction(function () use ($handle, &$header, &$count, &$created, &$updated, &$skipped) {
            while (($row = fgetcsv($handle)) !== false) {
                if (!$header) { $header = array_map(fn($h) => strtolower(trim($h)), $row); continue; }
                $count++;
                $data = $this->combineRow($header, $row);

                $email = $data['user_email'] ?? null;
                if (!$email) { $skipped++; continue; }
                $user = User::where('email', $email)->first();
                if (!$user) { $skipped++; continue; }

                $course = null;
                if (!empty($data['course_id'])) {
                    $course = Course::find($data['course_id']);
                }
                if (!$course && !empty($data['course_name'])) {
                    $course = Course::where('name', $data['course_name'])->first();
                }
                if (!$course) { $skipped++; continue; }

                $attrs = [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'enrolled_at' => !empty($data['enrolled_at']) ? Carbon::parse($data['enrolled_at']) : now(),
                    'expires_at' => !empty($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
                    'is_active' => $this->toBool($data['is_active'] ?? 1),
                    'progress_percentage' => isset($data['progress_percentage']) ? floatval($data['progress_percentage']) : 0,
                ];

                $existing = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
                if ($existing) { $existing->update($attrs); $updated++; } else { Enrollment::create($attrs); $created++; }
            }
        });
        fclose($handle);

        return back()->with('success', "Import iscrizioni completato. Righe lette: {$count}, creati: {$created}, aggiornati: {$updated}, scartati: {$skipped}.");
    }

    public function importPayments(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/octet-stream,application/csv',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Impossibile leggere il file CSV.');
        }

        $header = null; $count = 0; $created = 0; $updated = 0; $skipped = 0;
        DB::transaction(function () use ($handle, &$header, &$count, &$created, &$updated, &$skipped) {
            while (($row = fgetcsv($handle)) !== false) {
                if (!$header) { $header = array_map(fn($h) => strtolower(trim($h)), $row); continue; }
                $count++;
                $data = $this->combineRow($header, $row);

                $email = $data['user_email'] ?? null;
                if (!$email) { $skipped++; continue; }
                $user = User::where('email', $email)->first();
                if (!$user) { $skipped++; continue; }

                $course = null;
                if (!empty($data['course_id'])) { $course = Course::find($data['course_id']); }
                if (!$course && !empty($data['course_name'])) { $course = Course::where('name', $data['course_name'])->first(); }
                if (!$course) { $skipped++; continue; }

                // Identify existing payment by provider ids if available
                $existing = null;
                foreach (['stripe_payment_intent_id', 'paypal_capture_id', 'stripe_session_id', 'paypal_order_id'] as $key) {
                    if (!empty($data[$key])) {
                        $existing = Payment::where($key, $data[$key])->first();
                        if ($existing) { break; }
                    }
                }

                $attrs = [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'provider' => $data['provider'] ?? 'import',
                    'stripe_session_id' => $data['stripe_session_id'] ?? null,
                    'stripe_payment_intent_id' => $data['stripe_payment_intent_id'] ?? null,
                    'paypal_order_id' => $data['paypal_order_id'] ?? null,
                    'paypal_capture_id' => $data['paypal_capture_id'] ?? null,
                    'amount_total' => isset($data['amount_total']) ? intval($data['amount_total']) : 0,
                    'currency' => $data['currency'] ?? 'EUR',
                    'status' => $data['status'] ?? 'paid',
                    'customer_email' => $data['customer_email'] ?? $email,
                ];

                if ($existing) { $existing->update($attrs); $updated++; }
                else { Payment::create($attrs); $created++; }
            }
        });
        fclose($handle);

        return back()->with('success', "Import pagamenti completato. Righe lette: {$count}, creati: {$created}, aggiornati: {$updated}, scartati: {$skipped}.");
    }

    // =========================
    // Helpers
    // =========================

    private function streamCsv(array $rows, string $filename)
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) { fputcsv($handle, $row); }
            fclose($handle);
        }, $filename, [ 'Content-Type' => 'text/csv' ]);
    }

    private function combineRow(array $header, array $row): array
    {
        $data = [];
        foreach ($header as $i => $key) {
            $data[$key] = $row[$i] ?? null;
        }
        return $data;
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) return $value;
        $v = strtolower((string)$value);
        return in_array($v, ['1','true','yes','y','on'], true);
    }
}

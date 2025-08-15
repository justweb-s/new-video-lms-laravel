<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('catalog.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        if (!$course->is_active) {
            abort(404);
        }

        $isEnrolled = false;
        if (Auth::check()) {
            $user = Auth::user();
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->where('is_active', true)
                ->first();
            $isEnrolled = $enrollment && !$enrollment->isExpired();
        }

        return view('catalog.courses.show', compact('course', 'isEnrolled'));
    }

    public function purchase(Request $request, Course $course)
    {
        $user = $request->user();

        if (!$course->is_active) {
            return redirect()->route('catalog.show', $course)
                ->with('error', 'Il corso non è attualmente disponibile.');
        }

        // Se già iscritto e attivo, vai direttamente al corso
        $existing = $user->enrollments()->where('course_id', $course->id)->first();
        if ($existing && $existing->isActive()) {
            return redirect()->route('courses.show', $course)
                ->with('status', 'Sei già iscritto a questo corso.');
        }

        // Configura Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        $amount = (int) round($course->price * 100); // in centesimi
        $currency = config('services.stripe.currency', 'eur');

        $successUrl = route('catalog.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&course=' . $course->id;
        $cancelUrl = route('catalog.checkout.cancel') . '?course=' . $course->id;

        // Crea la Checkout Session
        $session = StripeSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $amount,
                    'product_data' => [
                        'name' => $course->name,
                        'description' => Str::limit((string) $course->description, 200),
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'course_id' => (string) $course->id,
                'user_id' => (string) $user->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'course' => 'required|integer',
        ]);

        $user = $request->user();
        $course = Course::findOrFail($request->integer('course'));

        // Verifica sessione Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($request->string('session_id'));

        if (!in_array($session->payment_status, ['paid'], true)) {
            return redirect()->route('catalog.show', $course)
                ->with('error', 'Pagamento non confermato.');
        }

        // Verifica che la sessione appartenga allo stesso utente e corso
        $metaUserId = (string)($session->metadata['user_id'] ?? '');
        $metaCourseId = (string)($session->metadata['course_id'] ?? '');
        if ($metaUserId !== (string) $user->id || $metaCourseId !== (string) $course->id) {
            return redirect()->route('catalog.show', $course)
                ->with('error', 'Verifica pagamento non valida.');
        }

        // Verifica importo e valuta coerenti col corso
        $expectedAmount = (int) round(((float) $course->price) * 100);
        $expectedCurrency = strtolower(config('services.stripe.currency', 'eur'));
        if ((int) $session->amount_total < $expectedAmount || strtolower($session->currency) !== $expectedCurrency) {
            return redirect()->route('catalog.show', $course)
                ->with('error', 'Dati pagamento incoerenti.');
        }

        // Crea o attiva l'iscrizione
        $enrollment = Enrollment::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $expiresAt = null;
        if (!empty($course->duration_weeks)) {
            $expiresAt = now()->addWeeks($course->duration_weeks);
        }

        $enrollment->enrolled_at = now();
        $enrollment->expires_at = $expiresAt;
        $enrollment->is_active = true;
        $enrollment->progress_percentage = $enrollment->progress_percentage ?? 0;
        $enrollment->save();

        return redirect()->route('courses.show', $course)
            ->with('status', 'Pagamento completato! Iscrizione attivata.');
    }

    public function cancel(Request $request)
    {
        $courseId = $request->integer('course');
        $course = Course::find($courseId);
        if ($course) {
            return redirect()->route('catalog.show', $course)
                ->with('error', 'Pagamento annullato.');
        }
        return redirect()->route('catalog.index')->with('error', 'Pagamento annullato.');
    }
}

<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\GiftCard;
use App\Models\Setting;
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

        // Se è stato fornito un codice gift card valido, usa quello come pagamento ed evita Stripe
        $giftCode = trim((string) $request->query('gift_code', ''));
        if ($giftCode !== '') {
            $gift = GiftCard::where('code', strtoupper($giftCode))->first();
            if (!$gift) {
                return redirect()->route('catalog.show', $course)
                    ->with('error', 'Codice gift card non trovato.');
            }
            if ($gift->status !== 'paid' || $gift->redeemed_at) {
                return redirect()->route('catalog.show', $course)
                    ->with('error', 'Codice gift card non valido o già utilizzato.');
            }
            if ((int) $gift->course_id !== (int) $course->id) {
                return redirect()->route('catalog.show', $course)
                    ->with('error', 'Questa gift card non è valida per questo corso.');
            }

            // Redeem gift card
            $gift->status = 'redeemed';
            $gift->redeemed_at = now();
            $gift->redeemer_user_id = $user->id;
            $gift->save();

            // Registra un Payment per tracciabilità amministrativa (0 revenue, redemption)
            try {
                Payment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'stripe_session_id' => null,
                    'stripe_payment_intent_id' => null,
                    'amount_total' => 0,
                    'currency' => strtolower(config('services.stripe.currency', 'eur')),
                    'status' => 'gift_redeemed',
                    'customer_email' => $user->email,
                    'customer_details' => null,
                    'custom_fields' => null,
                    'metadata' => [
                        'type' => 'gift_card_redemption',
                        'gift_card_code' => $gift->code,
                        'gift_card_amount' => $gift->amount,
                    ],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Impossibile registrare Payment redemption gift card: ' . $e->getMessage());
            }

            // Crea o attiva l'iscrizione
            $enrollment = Enrollment::firstOrNew([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            // Scadenza automatica a N giorni (default 30) dalla redemption
            $defaultDays = (int) (Setting::get('enrollment.default_duration_days', 30));
            $expiresAt = now()->addDays(max(1, $defaultDays));

            $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
            $enrollment->expires_at = $expiresAt;
            $enrollment->is_active = true;
            $enrollment->progress_percentage = $enrollment->progress_percentage ?? 0;
            $enrollment->save();

            return redirect()->route('courses.show', $course)
                ->with('status', 'Iscrizione attivata tramite gift card. Buona visione!');
        }

        // Configura Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        $amount = (int) round($course->price * 100); // in centesimi
        $currency = config('services.stripe.currency', 'eur');

        $successUrl = route('catalog.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&course=' . $course->id;
        $cancelUrl = route('catalog.checkout.cancel') . '?course=' . $course->id;

        // Crea la Checkout Session con raccolta dati aggiuntivi
        $session = StripeSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'billing_address_collection' => 'required',
            'customer_email' => $user->email,
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
            'phone_number_collection' => [
                'enabled' => true,
            ],
            'tax_id_collection' => [
                'enabled' => true,
            ],
            'custom_fields' => [
                [
                    'key' => 'codice_fiscale',
                    'label' => [
                        'type' => 'custom',
                        'custom' => 'Codice Fiscale (se non hai P.IVA)',
                    ],
                    'type' => 'text',
                    'text' => [
                        'maximum_length' => 16,
                        'minimum_length' => 16,
                    ],
                    'optional' => true,
                ],
                [
                    'key' => 'vat_number',
                    'label' => [
                        'type' => 'custom',
                        'custom' => 'Partita IVA (se azienda)',
                    ],
                    'type' => 'text',
                    'text' => [
                        'maximum_length' => 20,
                        'minimum_length' => 8,
                    ],
                    'optional' => true,
                ],
            ],
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

        // Salva i dati raccolti su utente (telefono, indirizzo, CF, VAT)
        if ($details = $session->customer_details) {
            // Telefono
            if (!empty($details->phone)) {
                $user->phone = $details->phone;
            }
            // Indirizzo di fatturazione
            if (!empty($details->address)) {
                $addr = $details->address;
                $user->billing_address_line1 = $addr->line1 ?? $user->billing_address_line1;
                $user->billing_address_line2 = $addr->line2 ?? $user->billing_address_line2;
                $user->billing_city = $addr->city ?? $user->billing_city;
                $user->billing_state = $addr->state ?? $user->billing_state;
                $user->billing_postal_code = $addr->postal_code ?? $user->billing_postal_code;
                $user->billing_country = $addr->country ?? $user->billing_country;
            }
            // Tax IDs (es. P.IVA)
            if (!empty($details->tax_ids) && is_array($details->tax_ids)) {
                // Prende il primo disponibile
                $firstTaxId = $details->tax_ids[0] ?? null;
                if ($firstTaxId && !empty($firstTaxId->value)) {
                    $user->tax_id = $firstTaxId->value;
                }
            }
        }

        // Campi personalizzati: Codice Fiscale e P.IVA
        $taxCode = null;
        $vatNumber = null;
        if (!empty($session->custom_fields) && is_array($session->custom_fields)) {
            foreach ($session->custom_fields as $field) {
                if (($field->key ?? null) === 'codice_fiscale') {
                    $taxCode = $field->text->value ?? null;
                }
                if (($field->key ?? null) === 'vat_number') {
                    $vatNumber = $field->text->value ?? null;
                }
            }
        }
        if (!empty($taxCode)) {
            $user->tax_code = $taxCode;
        }
        if (!empty($vatNumber)) {
            // Salva la P.IVA nel campo tax_id dell'utente, se non già presente da tax_id_collection
            if (empty($user->tax_id)) {
                $user->tax_id = $vatNumber;
            }
        }

        $user->save();

        // Registra il pagamento per consultazione lato admin (idempotente)
        try {
            $customFieldsArr = null;
            if (!empty($session->custom_fields) && is_array($session->custom_fields)) {
                $customFieldsArr = [];
                foreach ($session->custom_fields as $cf) {
                    // Ogni elemento è uno StripeObject: convertiamolo in array
                    $customFieldsArr[] = method_exists($cf, 'toArray') ? $cf->toArray() : json_decode(json_encode($cf), true);
                }
            }

            $customerDetailsArr = $session->customer_details ? (method_exists($session->customer_details, 'toArray') ? $session->customer_details->toArray() : json_decode(json_encode($session->customer_details), true)) : null;
            $metadataArr = $session->metadata ? (method_exists($session->metadata, 'toArray') ? $session->metadata->toArray() : json_decode(json_encode($session->metadata), true)) : null;

            $attributes = [
                'stripe_session_id' => (string) $session->id,
            ];
            $billingAddress = $session->customer_details->address ?? null;

            $values = [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'stripe_payment_intent_id' => is_string($session->payment_intent) ? $session->payment_intent : (is_object($session->payment_intent) ? ($session->payment_intent->id ?? null) : null),
                'amount_total' => (int) $session->amount_total,
                'currency' => (string) strtolower($session->currency),
                'status' => (string) $session->payment_status,
                'customer_email' => $session->customer_details->email ?? $session->customer_email ?? null,
                'customer_details' => $customerDetailsArr,
                'custom_fields' => $customFieldsArr,
                'metadata' => $metadataArr,
                // Dati di fatturazione
                'billing_name' => $session->customer_details->name ?? null,
                'vat_number' => $vatNumber, // Dal campo custom
                'billing_address_line1' => $billingAddress ? $billingAddress->line1 : null,
                'billing_address_line2' => $billingAddress ? $billingAddress->line2 : null,
                'billing_address_city' => $billingAddress ? $billingAddress->city : null,
                'billing_address_state' => $billingAddress ? $billingAddress->state : null,
                'billing_address_postal_code' => $billingAddress ? $billingAddress->postal_code : null,
                'billing_address_country' => $billingAddress ? $billingAddress->country : null,
            ];

            Payment::updateOrCreate($attributes, $values);
        } catch (\Throwable $e) {
            // Non bloccare il flusso utente se per qualche ragione il salvataggio pagamento fallisce
            \Log::warning('Impossibile registrare Payment: ' . $e->getMessage());
        }

        // Crea o attiva l'iscrizione
        $enrollment = Enrollment::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Scadenza automatica a N giorni (default 30) dall'acquisto
        $defaultDays = (int) (Setting::get('enrollment.default_duration_days', 30));
        $expiresAt = now()->addDays(max(1, $defaultDays));

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

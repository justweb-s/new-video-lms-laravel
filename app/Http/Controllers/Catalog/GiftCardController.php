<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Mail\GiftCardIssuedMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GiftCard;
use App\Models\Setting;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Services\PayPalService;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class GiftCardController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->where('is_active', true)
            ->where('price', '>', 0)
            ->orderBy('name')
            ->get(['id','name','description','image_url','price']);

        return view('giftcards.index', compact('courses'));
    }

    public function show(Course $course)
    {
        abort_unless($course->is_active && $course->price > 0, 404);
        return view('giftcards.show', compact('course'));
    }

    public function checkout(Request $request, Course $course)
    {
        abort_unless($course->is_active && $course->price > 0, 404);

        // Se l'utente non è autenticato:
        // - se POST: salva i dati del form in sessione e imposta l'URL intended
        // - reindirizza al login (dove sarà possibile anche registrarsi)
        if (!Auth::check()) {
            if ($request->isMethod('post')) {
                $request->session()->put('gift_checkout_'.$course->id, [
                    'recipient_name' => (string) $request->input('recipient_name'),
                    'recipient_email' => (string) $request->input('recipient_email'),
                    'message' => (string) $request->input('message'),
                    'provider' => (string) $request->input('provider', 'stripe'),
                ]);
            }
            // Imposta manualmente l'URL di ritorno dopo login/registrazione
            $request->session()->put('url.intended', route('giftcards.checkout', $course));
            return redirect()->route('login')->with('status', "Accedi o registrati per completare l'acquisto.");
        }

        // Utente autenticato: se GET, riprendi eventuali dati salvati in sessione
        if ($request->isMethod('get')) {
            $saved = $request->session()->pull('gift_checkout_'.$course->id, null);
            if ($saved) {
                $request->merge($saved);
            } else {
                return redirect()->route('giftcards.show', $course)
                    ->with('error', 'Compila i dati del destinatario per proseguire.');
            }
        }

        // A questo punto abbiamo i dati nel request (da POST o da sessione): validiamo
        $validated = $request->validate([
            'recipient_name' => ['required','string','max:100'],
            'recipient_email' => ['required','email','max:150'],
            'message' => ['nullable','string','max:1000'],
        ]);

        $user = Auth::user();
        $provider = (string) $request->input('provider', (string) ($request->session()->get('gift_checkout_'.$course->id)['provider'] ?? 'stripe'));

        // Calcoli comuni
        $priceCents = (int) round(((float) $course->price) * 100);

        if ($provider === 'paypal') {
            // PayPal: crea ordine e reindirizza all'approvazione
            $amount = number_format(((float) $course->price), 2, '.', '');
            $successUrl = URL::route('giftcards.checkout.success', [], true) . '?course='.$course->id;
            $cancelUrl = URL::route('giftcards.checkout.cancel', [], true) . '?course='.$course->id;

            /** @var PayPalService $pp */
            $pp = app(PayPalService::class);
            $order = $pp->createOrder(
                amountValue: $amount,
                currencyCode: config('services.paypal.currency', 'eur'),
                returnUrl: $successUrl,
                cancelUrl: $cancelUrl,
                description: 'Gift Card - ' . $course->name,
                locale: app()->getLocale()
            );

            $orderId = (string) ($order['id'] ?? '');
            $approve = PayPalService::getApproveLink($order);
            if (!$orderId || !$approve) {
                return redirect()->route('giftcards.show', $course)->with('error', 'Impossibile iniziare il pagamento PayPal.');
            }

            // Salva dati gift per riprenderli al ritorno
            $request->session()->put('gift_pp_'.$orderId, [
                'recipient_name' => (string) $validated['recipient_name'],
                'recipient_email' => (string) $validated['recipient_email'],
                'message' => (string) ($validated['message'] ?? $request->input('message', '')),
            ]);

            return redirect()->away($approve);
        }

        // Default: Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $currency = strtolower(config('services.stripe.currency', 'eur'));
        $successUrl = URL::route('giftcards.checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}&course='.$course->id;
        $cancelUrl = URL::route('giftcards.checkout.cancel', [], true) . '?course='.$course->id;

        $session = StripeSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Gift Card - ' . $course->name,
                        'description' => 'Buono regalo per il corso: ' . $course->name,
                    ],
                    'unit_amount' => $priceCents,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $user->email,
            'billing_address_collection' => 'auto',
            'phone_number_collection' => [ 'enabled' => true ],
            'allow_promotion_codes' => false,
            'metadata' => [
                'type' => 'gift_card',
                'course_id' => (string) $course->id,
                'buyer_user_id' => (string) $user->id,
                'recipient_name' => (string) $validated['recipient_name'],
                'recipient_email' => (string) $validated['recipient_email'],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'locale' => app()->getLocale(),
        ]);

        // Memorizza temporaneamente il messaggio in cache di sessione per riprenderlo al successo
        $request->session()->put('gift_msg_'.$session->id, (string) ($validated['message'] ?? $request->input('message', '')));

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = (string) $request->query('session_id');
        $orderId = (string) $request->query('token'); // PayPal order id
        $courseId = (int) $request->query('course');
        if ((!$sessionId && !$orderId) || !$courseId) {
            return redirect()->route('giftcards.index')->with('error', 'Sessione non valida.');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Accedi per completare l\'acquisto.');
        }

        $course = Course::findOrFail($courseId);

        // Flusso PayPal
        if ($orderId) {
            /** @var PayPalService $pp */
            $pp = app(PayPalService::class);
            try {
                $capture = $pp->captureOrder($orderId);
            } catch (\Throwable $e) {
                return redirect()->route('giftcards.show', $course)->with('error', 'Pagamento PayPal non confermato.');
            }

            $status = (string) ($capture['status'] ?? '');
            if (!in_array($status, ['COMPLETED'], true)) {
                return redirect()->route('giftcards.show', $course)->with('error', 'Pagamento PayPal non confermato.');
            }

            $pu = $capture['purchase_units'][0] ?? [];
            $cap = ($pu['payments']['captures'][0] ?? []);
            $amountValue = (string) ($cap['amount']['value'] ?? '0.00');
            $currency = strtolower((string) ($cap['amount']['currency_code'] ?? config('services.paypal.currency', 'eur')));
            $amountCents = (int) round(((float) $amountValue) * 100);
            $payer = $capture['payer'] ?? null;
            $customerEmail = $payer['email_address'] ?? null;

            // Recupera dati gift salvati prima del redirect
            $giftData = $request->session()->pull('gift_pp_'.$orderId, null);
            $recipientName = (string) ($giftData['recipient_name'] ?? '');
            $recipientEmail = (string) ($giftData['recipient_email'] ?? $customerEmail ?? '');
            $message = (string) ($giftData['message'] ?? '');

            // Crea/aggiorna Payment
            try {
                $attributes = [ 'paypal_order_id' => (string) $orderId ];
                $values = [
                    'provider' => 'paypal',
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'paypal_capture_id' => (string) ($cap['id'] ?? ''),
                    'amount_total' => $amountCents,
                    'currency' => $currency,
                    'status' => 'paid',
                    'customer_email' => $customerEmail,
                    'customer_details' => $payer,
                    'custom_fields' => null,
                    'metadata' => null,
                ];
                Payment::updateOrCreate($attributes, $values);
            } catch (\Throwable $e) {
                \Log::warning('Impossibile registrare Payment PayPal GiftCard: ' . $e->getMessage());
            }

            // Genera codice univoco
            $code = null;
            for ($i=0; $i<5; $i++) {
                $candidate = 'GC-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
                if (!GiftCard::where('code', $candidate)->exists()) { $code = $candidate; break; }
            }
            if (!$code) { $code = 'GC-' . strtoupper(Str::random(10)); }

            // Crea GiftCard (idempotente su paypal_order_id)
            $gift = GiftCard::firstOrCreate(
                ['paypal_order_id' => (string) $orderId],
                [
                    'code' => $code,
                    'course_id' => $course->id,
                    'buyer_user_id' => $user->id,
                    'recipient_name' => $recipientName,
                    'recipient_email' => $recipientEmail,
                    'message' => $message,
                    'amount' => $amountCents,
                    'currency' => $currency,
                    'provider' => 'paypal',
                    'status' => 'paid',
                    'paypal_capture_id' => (string) ($cap['id'] ?? ''),
                    'issued_at' => now(),
                ]
            );

            try { Mail::to($gift->recipient_email)->send(new GiftCardIssuedMail($gift)); } catch (\Throwable $e) { \Log::error('Errore invio email gift card: ' . $e->getMessage()); }

            return redirect()->route('giftcards.show', $course)->with('status', 'Gift card acquistata! Il destinatario riceverà una email con il codice.');
        }

        // Flusso Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($sessionId);

        if (!in_array($session->payment_status, ['paid'], true)) {
            return redirect()->route('giftcards.show', $course)->with('error', 'Pagamento non confermato.');
        }

        // Validazioni base
        if (($session->metadata['type'] ?? '') !== 'gift_card' || (int)($session->metadata['course_id'] ?? 0) !== $course->id) {
            return redirect()->route('giftcards.show', $course)->with('error', 'Dati pagamento non validi.');
        }

        $expectedAmount = (int) round(((float) $course->price) * 100);
        $expectedCurrency = strtolower(config('services.stripe.currency', 'eur'));
        if ((int) $session->amount_total < $expectedAmount || strtolower($session->currency) !== $expectedCurrency) {
            return redirect()->route('giftcards.show', $course)->with('error', 'Dati pagamento incoerenti.');
        }

        // Recupera messaggio dalla sessione
        $message = $request->session()->pull('gift_msg_'.$session->id);

        // Crea Payment (idempotente)
        try {
            $customerDetailsArr = $session->customer_details ? (method_exists($session->customer_details, 'toArray') ? $session->customer_details->toArray() : json_decode(json_encode($session->customer_details), true)) : null;
            $metadataArr = $session->metadata ? (method_exists($session->metadata, 'toArray') ? $session->metadata->toArray() : json_decode(json_encode($session->metadata), true)) : null;

            $attributes = [ 'stripe_session_id' => (string) $session->id ];
            $values = [
                'provider' => 'stripe',
                'user_id' => $user->id,
                'course_id' => $course->id,
                'stripe_payment_intent_id' => is_string($session->payment_intent) ? $session->payment_intent : (is_object($session->payment_intent) ? ($session->payment_intent->id ?? null) : null),
                'amount_total' => (int) $session->amount_total,
                'currency' => (string) strtolower($session->currency),
                'status' => (string) $session->payment_status,
                'customer_email' => $session->customer_details->email ?? $session->customer_email ?? null,
                'customer_details' => $customerDetailsArr,
                'custom_fields' => null,
                'metadata' => $metadataArr,
            ];
            Payment::updateOrCreate($attributes, $values);
        } catch (\Throwable $e) {
            \Log::warning('Impossibile registrare Payment GiftCard: ' . $e->getMessage());
        }

        // Genera codice univoco
        $code = null;
        for ($i=0; $i<5; $i++) {
            $candidate = 'GC-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            if (!GiftCard::where('code', $candidate)->exists()) { $code = $candidate; break; }
        }
        if (!$code) {
            $code = 'GC-' . strtoupper(Str::random(10));
        }

        // Crea GiftCard (idempotente su sessione)
        $gift = GiftCard::firstOrCreate(
            ['stripe_session_id' => (string) $session->id],
            [
                'code' => $code,
                'course_id' => $course->id,
                'buyer_user_id' => $user->id,
                'recipient_name' => (string) ($session->metadata['recipient_name'] ?? ''),
                'recipient_email' => (string) ($session->metadata['recipient_email'] ?? ''),
                'message' => (string) ($message ?? ''),
                'amount' => (int) $session->amount_total,
                'currency' => (string) strtolower($session->currency),
                'provider' => 'stripe',
                'status' => 'paid',
                'stripe_payment_intent_id' => is_string($session->payment_intent) ? $session->payment_intent : (is_object($session->payment_intent) ? ($session->payment_intent->id ?? null) : null),
                'issued_at' => now(),
            ]
        );

        try {
            Mail::to($gift->recipient_email)->send(new GiftCardIssuedMail($gift));
        } catch (\Throwable $e) {
            \Log::error('Errore invio email gift card: ' . $e->getMessage());
        }

        return redirect()->route('giftcards.show', $course)->with('status', 'Gift card acquistata! Il destinatario riceverà una email con il codice.');
    }

    public function cancel(Request $request)
    {
        $courseId = (int) $request->query('course');
        if ($courseId) {
            $course = Course::find($courseId);
            if ($course) {
                return redirect()->route('giftcards.show', $course)->with('error', 'Pagamento annullato.');
            }
        }
        return redirect()->route('giftcards.index')->with('error', 'Pagamento annullato.');
    }

    public function redeemForm(Request $request)
    {
        $code = (string) $request->query('code', '');
        $gift = null;
        if ($code) {
            $gift = GiftCard::where('code', $code)->first();
        }
        return view('giftcards.redeem', compact('code','gift'));
    }

    public function redeem(Request $request)
    {
        $request->validate(['code' => ['required','string','max:50']]);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Accedi per riscattare una gift card.');
        }

        $gift = GiftCard::where('code', $request->code)->first();
        if (!$gift || $gift->status !== 'paid' || $gift->redeemed_at) {
            return redirect()->back()->with('error', 'Codice non valido o già utilizzato.');
        }

        $course = Course::find($gift->course_id);
        if (!$course || !$course->is_active) {
            return redirect()->back()->with('error', 'Il corso associato non è disponibile.');
        }

        // Crea o attiva l'iscrizione
        $enrollment = Enrollment::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
        $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
        // Scadenza automatica a N giorni (default 30) dal riscatto
        $defaultDays = (int) (Setting::get('enrollment.default_duration_days', 30));
        $enrollment->expires_at = now()->addDays(max(1, $defaultDays));
        $enrollment->is_active = true;
        $enrollment->save();

        // Marca la gift card come riscattata
        $gift->status = 'redeemed';
        $gift->redeemed_at = now();
        $gift->redeemer_user_id = $user->id;
        $gift->save();

        return redirect()->route('catalog.show', $course)->with('status', 'Gift card riscattata! Ora hai accesso al corso.');
    }
}

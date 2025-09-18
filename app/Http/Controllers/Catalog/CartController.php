<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Mail\GiftCardIssuedMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GiftCard;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\PayPalService;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CartController extends Controller
{
    private function getItems(Request $request): array
    {
        return (array) $request->session()->get('cart.items', []);
    }

    private function putItems(Request $request, array $items): void
    {
        $request->session()->put('cart.items', array_values($items));
    }

    public function index(Request $request)
    {
        $items = $this->getItems($request);
        // Ricalcola i prezzi dal DB per sicurezza
        $total = 0;
        foreach ($items as &$it) {
            $course = Course::find($it['course_id'] ?? null);
            if ($course && $course->is_active && $course->price > 0) {
                $it['name'] = $it['type'] === 'gift_card' ? ('Gift Card - ' . $course->name) : $course->name;
                $it['price'] = (float) $course->price;
                $total += $it['price'];
            } else {
                $it['invalid'] = true;
            }
        }
        unset($it);

        return view('cart.index', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function addCourse(Request $request, Course $course)
    {
        abort_unless($course->is_active && $course->price > 0, 404);
        $items = $this->getItems($request);

        // Evita duplicati dello stesso corso (un solo acquisto per corso)
        foreach ($items as $it) {
            if (($it['type'] ?? '') === 'course' && (int)($it['course_id'] ?? 0) === (int) $course->id) {
                return redirect()->route('cart.index')->with('status', 'Il corso è già nel carrello.');
            }
        }

        $items[] = [
            'id' => (string) Str::uuid(),
            'type' => 'course',
            'course_id' => $course->id,
            'name' => $course->name,
            'price' => (float) $course->price,
            'image' => (string) ($course->image_url ?? ''),
        ];

        $this->putItems($request, $items);

        if ($request->expectsJson()) {
            $count = count($items);
            $image = $course->image_url;
            $imageUrl = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : Storage::url($image)) : null;
            return response()->json([
                'ok' => true,
                'message' => 'Corso aggiunto al carrello.',
                'count' => $count,
                'item' => [
                    'id' => end($items)['id'],
                    'type' => 'course',
                    'course_id' => $course->id,
                    'name' => $course->name,
                    'price' => (float) $course->price,
                    'image' => $imageUrl,
                ],
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Corso aggiunto al carrello.');
    }

    public function addGiftCard(Request $request, Course $course)
    {
        abort_unless($course->is_active && $course->price > 0, 404);
        $validated = $request->validate([
            'recipient_name' => ['required','string','max:100'],
            'recipient_email' => ['required','email','max:150'],
            'message' => ['nullable','string','max:1000'],
        ]);

        $items = $this->getItems($request);
        $items[] = [
            'id' => (string) Str::uuid(),
            'type' => 'gift_card',
            'course_id' => $course->id,
            'name' => 'Gift Card - ' . $course->name,
            'price' => (float) $course->price,
            'gift' => [
                'recipient_name' => (string) $validated['recipient_name'],
                'recipient_email' => (string) $validated['recipient_email'],
                'message' => (string) ($validated['message'] ?? ''),
            ],
            'image' => (string) ($course->image_url ?? ''),
        ];

        $this->putItems($request, $items);

        if ($request->expectsJson()) {
            $count = count($items);
            $image = $course->image_url;
            $imageUrl = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : Storage::url($image)) : null;
            return response()->json([
                'ok' => true,
                'message' => 'Gift card aggiunta al carrello.',
                'count' => $count,
                'item' => [
                    'id' => end($items)['id'],
                    'type' => 'gift_card',
                    'course_id' => $course->id,
                    'name' => 'Gift Card - ' . $course->name,
                    'price' => (float) $course->price,
                    'image' => $imageUrl,
                    'gift' => [
                        'recipient_name' => (string) $validated['recipient_name'],
                        'recipient_email' => (string) $validated['recipient_email'],
                        'message' => (string) ($validated['message'] ?? ''),
                    ],
                ],
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Gift card aggiunta al carrello.');
    }

    public function remove(Request $request, string $id)
    {
        $items = $this->getItems($request);
        $items = array_values(array_filter($items, fn ($it) => ($it['id'] ?? null) !== $id));
        $this->putItems($request, $items);
        return redirect()->route('cart.index')->with('status', 'Elemento rimosso dal carrello.');
    }

    public function clear(Request $request)
    {
        $this->putItems($request, []);
        return redirect()->route('cart.index')->with('status', 'Carrello svuotato.');
    }

    public function checkout(Request $request)
    {
        $items = $this->getItems($request);
        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Il carrello è vuoto.');
        }

        if (!Auth::check()) {
            // Dopo login/registrazione torna qui
            $request->session()->put('url.intended', route('cart.checkout'));
            return redirect()->route('login')->with('status', 'Accedi o registrati per procedere al pagamento.');
        }

        // Ricarica dal DB e costruisci line_items
        $lineItems = [];
        $validItems = [];
        $totalCents = 0;
        foreach ($items as $it) {
            $course = Course::find($it['course_id'] ?? null);
            if (!$course || !$course->is_active || !($course->price > 0)) {
                continue;
            }
            $priceCents = (int) round(((float) $course->price) * 100);
            $totalCents += $priceCents;

            if (($it['type'] ?? '') === 'gift_card') {
                $name = 'Gift Card - ' . $course->name;
                $description = 'Buono regalo per il corso: ' . $course->name;
            } else {
                $name = $course->name;
                $description = Str::limit((string) $course->description, 200);
            }

            $description = trim((string) $description);
            $productData = [ 'name' => $name ];
            if ($description !== '') {
                $productData['description'] = $description;
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(config('services.stripe.currency', 'eur')),
                    'product_data' => $productData,
                    'unit_amount' => $priceCents,
                ],
                'quantity' => 1,
            ];
            $validItems[] = $it;
        }

        if (empty($validItems)) {
            return redirect()->route('cart.index')->with('error', 'Nessun elemento valido nel carrello.');
        }

        // Scegli provider
        $provider = (string) $request->query('provider', 'stripe');

        if ($provider === 'paypal') {
            // PayPal: crea ordine per l'importo totale e reindirizza all'approvazione
            $amount = number_format(((float) ($totalCents / 100)), 2, '.', '');
            $pp = app(PayPalService::class);
            $successUrl = URL::route('cart.checkout.success', [], true);
            $cancelUrl = URL::route('cart.checkout.cancel', [], true);

            $order = $pp->createOrder(
                amountValue: $amount,
                currencyCode: config('services.paypal.currency', 'eur'),
                returnUrl: $successUrl,
                cancelUrl: $cancelUrl,
                description: 'Acquisto corsi/gift card',
                locale: app()->getLocale()
            );
            $orderId = (string) ($order['id'] ?? '');
            $approve = PayPalService::getApproveLink($order);
            if (!$orderId || !$approve) {
                return redirect()->route('cart.index')->with('error', 'Impossibile iniziare il pagamento PayPal.');
            }

            // Salva gli elementi per l'elaborazione post-pagamento
            Log::info('Cart.checkout pending saved (paypal)', [
                'lookup_key' => $orderId,
                'items_count' => count($validItems),
            ]);
            $request->session()->put('cart_pending_' . $orderId, $validItems);

            return redirect()->away($approve);
        }

        // Default Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        $token = (string) Str::uuid();
        $successUrl = URL::route('cart.checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}&cart_token=' . $token;
        $cancelUrl = URL::route('cart.checkout.cancel', [], true) . '?cart_token=' . $token;
        Log::info('Cart.checkout stripe urls', [
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'token' => $token,
        ]);

        $session = StripeSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'customer_email' => Auth::user()->email,
            'billing_address_collection' => 'auto',
            'phone_number_collection' => ['enabled' => true],
            'allow_promotion_codes' => false,
            'metadata' => [
                'type' => 'cart',
                'cart_token' => $token,
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'locale' => app()->getLocale(),
        ]);

        // Salva gli elementi per l'elaborazione post-pagamento
        Log::info('Cart.checkout pending saved (stripe)', [
            'lookup_key' => $token,
            'session_id' => $session->id,
            'items_count' => count($validItems),
        ]);
        $request->session()->put('cart_pending_' . $token, $validItems);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        // Logger dedicato su file per debug ordine
        $orderLogger = \Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/order-debug.log'),
            'level' => 'debug',
        ]);
        $sessionId = (string) $request->query('session_id');
        $ppOrderId = (string) $request->query('token'); // PayPal order id
        $token = (string) $request->query('cart_token');
        Log::info('Cart.success start', [
            'session_id' => $sessionId,
            'ppOrderId' => $ppOrderId,
            'token' => $token,
            'query' => $request->query(),
            'user_id' => optional(Auth::user())->id,
        ]);
        $orderLogger->info('Cart.success start', [
            'session_id' => $sessionId,
            'ppOrderId' => $ppOrderId,
            'token' => $token,
            'query' => $request->query(),
            'user_id' => optional(Auth::user())->id,
        ]);
        if (!$sessionId && !$ppOrderId && !$token) {
            Log::warning('Cart.success invalid session', [
                'session_id' => $sessionId,
                'ppOrderId' => $ppOrderId,
                'token' => $token,
            ]);
            $orderLogger->warning('Cart.success invalid session', [
                'session_id' => $sessionId,
                'ppOrderId' => $ppOrderId,
                'token' => $token,
            ]);
            return redirect()->route('cart.index')->with('error', 'Sessione non valida.');
        }

        $user = Auth::user();
        if (!$user) {
            Log::warning('Cart.success no auth user');
            $orderLogger->warning('Cart.success no auth user');
            return redirect()->route('login')->with('error', 'Accedi per completare l\'acquisto.');
        }

        // Per PayPal utilizziamo token=orderId come chiave, per Stripe cart_token
        $lookupKey = $ppOrderId ?: $token;
        $items = $request->session()->get('cart_pending_' . $lookupKey);
        Log::info('Cart.success loaded pending items', [
            'lookup_key' => $lookupKey,
            'items_count' => is_array($items) ? count($items) : null,
            'has_items' => is_array($items),
        ]);
        $orderLogger->info('Cart.success loaded pending items', [
            'lookup_key' => $lookupKey,
            'items_count' => is_array($items) ? count($items) : null,
            'has_items' => is_array($items),
        ]);
        if (!$items || !is_array($items)) {
            Log::warning('Cart.success cart not found or already processed', [
                'lookup_key' => $lookupKey,
            ]);
            $orderLogger->warning('Cart.success cart not found or already processed', [
                'lookup_key' => $lookupKey,
            ]);
            return redirect()->route('cart.index')->with('error', 'Carrello non trovato o già elaborato.');
        }
        
        // Variabili contesto pagamento (riusate nella creazione gift card)
        $paymentProvider = null;
        $paymentCurrencyCtx = null;
        $paymentStripeSessionId = null;
        $paymentStripePaymentIntentId = null;
        $paymentPayPalOrderId = null;
        $paymentPayPalCaptureId = null;

        // Per PayPal verificheremo l'importo contro il totale carrello
        $expectedTotalCents = 0;
        foreach ($items as $itm) {
            $crs = Course::find($itm['course_id'] ?? null);
            if ($crs && $crs->is_active && ($crs->price > 0)) {
                $expectedTotalCents += (int) round(((float) $crs->price) * 100);
            }
        }

        if ($ppOrderId) {
            // Flusso PayPal
            $pp = app(PayPalService::class);
            try {
                $capture = $pp->captureOrder($ppOrderId);
            } catch (\Throwable $e) {
                Log::error('Cart.success PayPal capture error', ['exception' => $e->getMessage()]);
                $orderLogger->error('Cart.success PayPal capture error', ['exception' => $e->getMessage()]);
                return redirect()->route('cart.index')->with('error', 'Pagamento non confermato.');
            }

            $status = (string) ($capture['status'] ?? '');
            if (!in_array($status, ['COMPLETED'], true)) {
                return redirect()->route('cart.index')->with('error', 'Pagamento non confermato.');
            }

            $pu = $capture['purchase_units'][0] ?? [];
            $cap = ($pu['payments']['captures'][0] ?? []);
            $amountValue = (string) ($cap['amount']['value'] ?? '0.00');
            $currency = strtolower((string) ($cap['amount']['currency_code'] ?? config('services.paypal.currency', 'eur')));
            $amountCents = (int) round(((float) $amountValue) * 100);
            $payer = $capture['payer'] ?? null;
            $customerEmail = $payer['email_address'] ?? null;

            // Convalida importo/valuta rispetto al carrello
            $expectedCurrency = strtolower(config('services.paypal.currency', 'eur'));
            if ($amountCents !== $expectedTotalCents || $currency !== $expectedCurrency) {
                return redirect()->route('cart.index')->with('error', 'Dati pagamento incoerenti.');
            }

            // Salva contesto provider per creazione gift card
            $paymentProvider = 'paypal';
            $paymentCurrencyCtx = $currency;
            $paymentPayPalOrderId = (string) $ppOrderId;
            $paymentPayPalCaptureId = (string) ($cap['id'] ?? '');
            Log::info('Cart.success provider set', [
                'provider' => $paymentProvider,
                'currency' => $paymentCurrencyCtx,
                'paypal_order_id' => $paymentPayPalOrderId,
                'paypal_capture_id' => $paymentPayPalCaptureId,
            ]);
            $orderLogger->info('Cart.success provider set', [
                'provider' => $paymentProvider,
                'currency' => $paymentCurrencyCtx,
                'paypal_order_id' => $paymentPayPalOrderId,
                'paypal_capture_id' => $paymentPayPalCaptureId,
            ]);
        } else {
            // Flusso Stripe
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($sessionId);
            if (!in_array($session->payment_status, ['paid'], true)) {
                return redirect()->route('cart.index')->with('error', 'Pagamento non confermato.');
            }

            // Salva contesto provider per creazione gift card
            $paymentProvider = 'stripe';
            $paymentCurrencyCtx = (string) strtolower($session->currency);
            $paymentStripeSessionId = (string) $session->id;
            $paymentStripePaymentIntentId = is_string($session->payment_intent) ? $session->payment_intent : (is_object($session->payment_intent) ? ($session->payment_intent->id ?? null) : null);
            Log::info('Cart.success provider set', [
                'provider' => $paymentProvider,
                'currency' => $paymentCurrencyCtx,
                'stripe_session_id' => $paymentStripeSessionId,
                'stripe_payment_intent_id' => $paymentStripePaymentIntentId,
            ]);
            $orderLogger->info('Cart.success provider set', [
                'provider' => $paymentProvider,
                'currency' => $paymentCurrencyCtx,
                'stripe_session_id' => $paymentStripeSessionId,
                'stripe_payment_intent_id' => $paymentStripePaymentIntentId,
            ]);
        }

        $createdPayments = [];
        Log::info('Cart.success processing items start', [
            'items_total' => is_array($items) ? count($items) : null,
        ]);
        $orderLogger->info('Cart.success processing items start', [
            'items_total' => is_array($items) ? count($items) : null,
        ]);

        // Elabora gli elementi
        foreach ($items as $it) {
            $course = Course::find($it['course_id'] ?? null);
            if (!$course || !$course->is_active) {
                continue;
            }

            if (($it['type'] ?? '') === 'gift_card') {
                // Crea gift card e invia email
                $code = null;
                for ($i = 0; $i < 5; $i++) {
                    $candidate = 'GC-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
                    if (!GiftCard::where('code', $candidate)->exists()) { $code = $candidate; break; }
                }
                if (!$code) { $code = 'GC-' . strtoupper(Str::random(10)); }

                $gift = GiftCard::create([
                    'code' => $code,
                    'course_id' => $course->id,
                    'buyer_user_id' => $user->id,
                    'recipient_name' => (string) ($it['gift']['recipient_name'] ?? ''),
                    'recipient_email' => (string) ($it['gift']['recipient_email'] ?? ''),
                    'message' => (string) ($it['gift']['message'] ?? ''),
                    'amount' => (int) round(((float) $course->price) * 100),
                    'currency' => (string) $paymentCurrencyCtx,
                    'provider' => $paymentProvider,
                    'status' => 'paid',
                    'stripe_session_id' => $paymentProvider === 'stripe' ? $paymentStripeSessionId : null,
                    'stripe_payment_intent_id' => $paymentProvider === 'stripe' ? $paymentStripePaymentIntentId : null,
                    'paypal_order_id' => $paymentProvider === 'paypal' ? $paymentPayPalOrderId : null,
                    'paypal_capture_id' => $paymentProvider === 'paypal' ? $paymentPayPalCaptureId : null,
                    'issued_at' => now(),
                ]);

                try { Mail::to($gift->recipient_email)->send(new GiftCardIssuedMail($gift)); } catch (\Throwable $e) { \Log::error('Errore invio email gift card: ' . $e->getMessage()); }
            } else {
                // Iscrizione al corso
                $enrollment = Enrollment::firstOrNew([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);
                // Scadenza automatica a N giorni (default 30) dall'acquisto
                $defaultDays = (int) (Setting::get('enrollment.default_duration_days', 30));
                $expiresAt = now()->addDays(max(1, $defaultDays));
                $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
                $enrollment->expires_at = $expiresAt;
                $enrollment->is_active = true;
                $enrollment->progress_percentage = $enrollment->progress_percentage ?? 0;
                $enrollment->save();
            }

            // Crea un record di pagamento per ogni articolo
            try {
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'provider' => $paymentProvider,
                    'stripe_session_id' => $paymentStripeSessionId,
                    'stripe_payment_intent_id' => $paymentStripePaymentIntentId,
                    'paypal_order_id' => $paymentPayPalOrderId,
                    'paypal_capture_id' => $paymentPayPalCaptureId,
                    'amount_total' => (int) round(((float) $course->price) * 100),
                    'currency' => $paymentCurrencyCtx,
                    'status' => 'paid',
                    'customer_email' => $user->email,
                    'metadata' => ['item_type' => $it['type']],
                ]);
                $createdPayments[] = $payment;
            } catch (\Throwable $e) {
                \Log::error('Errore creazione record di pagamento: ' . $e->getMessage());
            }
        }

        // Pulisci carrello e pending
        $request->session()->forget('cart.items');
        $request->session()->forget('cart_pending_' . $lookupKey);

        // Invia email di conferma ordine (su coda) con logging diagnostico
        Log::info('Cart.success created payments', [
            'count' => count($createdPayments),
        ]);
        $orderLogger->info('Cart.success created payments', [
            'count' => count($createdPayments),
        ]);
        if (!empty($createdPayments)) {
            try {
                $paymentIds = collect($createdPayments)->pluck('id')->all();
                Log::info('OrderConfirmationMail: dispatching', [
                    'user_id' => $user->id,
                    'payments_count' => count($createdPayments),
                    'payment_ids' => $paymentIds,
                ]);
                $orderLogger->info('OrderConfirmationMail: dispatching', [
                    'user_id' => $user->id,
                    'payments_count' => count($createdPayments),
                    'payment_ids' => $paymentIds,
                ]);
                if (config('queue.default') === 'sync') {
                    Mail::to($user)->send(new \App\Mail\OrderConfirmationMail($user, collect($createdPayments)));
                    Log::info('OrderConfirmationMail: sent (sync)', ['user_id' => $user->id]);
                    $orderLogger->info('OrderConfirmationMail: sent (sync)', ['user_id' => $user->id]);
                } else {
                    Mail::to($user)->queue(new \App\Mail\OrderConfirmationMail($user, collect($createdPayments)));
                    Log::info('OrderConfirmationMail: queued', [
                        'user_id' => $user->id,
                    ]);
                    $orderLogger->info('OrderConfirmationMail: queued', [
                        'user_id' => $user->id,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Errore invio email conferma ordine', [
                    'user_id' => $user->id,
                    'exception' => $e->getMessage(),
                ]);
                $orderLogger->error('Errore invio email conferma ordine', [
                    'user_id' => $user->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        } else {
            Log::info('OrderConfirmationMail: skipped (no payments)', [
                'user_id' => $user->id,
            ]);
            $orderLogger->info('OrderConfirmationMail: skipped (no payments)', [
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Pagamento completato! Acquisto effettuato.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('cart.index')->with('error', 'Pagamento annullato.');
    }

    public function state(Request $request)
    {
        $items = $this->getItems($request);
        $out = [];
        $total = 0;
        foreach ($items as $it) {
            $course = Course::find($it['course_id'] ?? null);
            if (!$course || !$course->is_active) {
                continue;
            }
            $price = (float) $course->price;
            $total += $price;
            $image = $course->image_url;
            $imageUrl = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : Storage::url($image)) : null;
            $row = [
                'id' => (string) ($it['id'] ?? ''),
                'type' => (string) ($it['type'] ?? ''),
                'course_id' => (int) $course->id,
                'name' => (string) (($it['type'] ?? '') === 'gift_card' ? ('Gift Card - ' . $course->name) : $course->name),
                'price' => (float) $price,
                'image' => $imageUrl,
            ];
            if (($it['type'] ?? '') === 'gift_card') {
                $row['gift'] = [
                    'recipient_name' => (string) ($it['gift']['recipient_name'] ?? ''),
                    'recipient_email' => (string) ($it['gift']['recipient_email'] ?? ''),
                    'message' => (string) ($it['gift']['message'] ?? ''),
                ];
            }
            $out[] = $row;
        }

        return response()->json([
            'ok' => true,
            'count' => count($out),
            'total' => $total,
            'items' => $out,
        ]);
    }
}

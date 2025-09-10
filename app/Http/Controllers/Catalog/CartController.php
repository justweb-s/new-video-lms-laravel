<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Mail\GiftCardIssuedMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GiftCard;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower(config('services.stripe.currency', 'eur')),
                    'product_data' => [
                        'name' => $name,
                        'description' => $description,
                    ],
                    'unit_amount' => $priceCents,
                ],
                'quantity' => 1,
            ];
            $validItems[] = $it;
        }

        if (empty($validItems)) {
            return redirect()->route('cart.index')->with('error', 'Nessun elemento valido nel carrello.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $token = (string) Str::uuid();
        $successUrl = URL::route('cart.checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}&cart_token=' . $token;
        $cancelUrl = URL::route('cart.checkout.cancel', [], true) . '?cart_token=' . $token;

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
        $request->session()->put('cart_pending_' . $token, $validItems);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = (string) $request->query('session_id');
        $token = (string) $request->query('cart_token');
        if (!$sessionId || !$token) {
            return redirect()->route('cart.index')->with('error', 'Sessione non valida.');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Accedi per completare l\'acquisto.');
        }

        $items = $request->session()->get('cart_pending_' . $token);
        if (!$items || !is_array($items)) {
            return redirect()->route('cart.index')->with('error', 'Carrello non trovato o già elaborato.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($sessionId);
        if (!in_array($session->payment_status, ['paid'], true)) {
            return redirect()->route('cart.index')->with('error', 'Pagamento non confermato.');
        }

        // Registra il pagamento
        try {
            $customerDetailsArr = $session->customer_details ? (method_exists($session->customer_details, 'toArray') ? $session->customer_details->toArray() : json_decode(json_encode($session->customer_details), true)) : null;
            $metadataArr = $session->metadata ? (method_exists($session->metadata, 'toArray') ? $session->metadata->toArray() : json_decode(json_encode($session->metadata), true)) : null;

            $attributes = [
                'stripe_session_id' => (string) $session->id,
            ];
            $values = [
                'user_id' => $user->id,
                'course_id' => null,
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
            \Log::warning('Impossibile registrare Payment cart: ' . $e->getMessage());
        }

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
                    'currency' => (string) strtolower(config('services.stripe.currency', 'eur')),
                    'status' => 'paid',
                    'stripe_session_id' => (string) $session->id,
                    'stripe_payment_intent_id' => is_string($session->payment_intent) ? $session->payment_intent : (is_object($session->payment_intent) ? ($session->payment_intent->id ?? null) : null),
                    'issued_at' => now(),
                ]);

                try { Mail::to($gift->recipient_email)->send(new GiftCardIssuedMail($gift)); } catch (\Throwable $e) { \Log::error('Errore invio email gift card: ' . $e->getMessage()); }
            } else {
                // Iscrizione al corso
                $enrollment = Enrollment::firstOrNew([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ]);
                $expiresAt = null;
                if (!empty($course->duration_weeks)) {
                    $expiresAt = now()->addWeeks($course->duration_weeks);
                }
                $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
                $enrollment->expires_at = $expiresAt;
                $enrollment->is_active = true;
                $enrollment->progress_percentage = $enrollment->progress_percentage ?? 0;
                $enrollment->save();
            }
        }

        // Pulisci carrello e pending
        $request->session()->forget('cart.items');
        $request->session()->forget('cart_pending_' . $token);

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

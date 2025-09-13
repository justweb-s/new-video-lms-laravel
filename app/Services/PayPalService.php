<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    private string $mode;
    private string $clientId;
    private string $secret;
    private string $currency;

    public function __construct()
    {
        $cfg = config('services.paypal');
        $this->mode = ($cfg['mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        $this->clientId = (string) ($cfg['client_id'] ?? '');
        $this->secret = (string) ($cfg['secret'] ?? '');
        $this->currency = strtoupper((string) ($cfg['currency'] ?? 'EUR'));
    }

    public function baseUrl(): string
    {
        // Usa i nuovi endpoint api-m.* consigliati da PayPal
        return $this->mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function getAccessToken(): string
    {
        $cacheKey = 'paypal_access_token_' . $this->mode;
        return Cache::remember($cacheKey, 300, function () {
            $url = $this->baseUrl() . '/v1/oauth2/token';
            $opts = [
                'connect_timeout' => 10,
                'timeout' => 20,
            ];
            if (\defined('CURLOPT_IPRESOLVE') && \defined('CURL_IPRESOLVE_V4')) {
                $opts['curl'] = [\CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4];
            }
            $response = Http::asForm()
                ->withOptions($opts)
                ->retry(2, 500)
                ->withBasicAuth($this->clientId, $this->secret)
                ->post($url, [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException('PayPal OAuth failed: ' . $response->body());
            }

            $data = $response->json();
            $token = (string) ($data['access_token'] ?? '');
            $expires = (int) ($data['expires_in'] ?? 300);
            // Aggiorna TTL cache per riflettere l'expire reale (meno 60s di margine)
            Cache::put('paypal_access_token_' . $this->mode, $token, max(60, $expires - 60));
            return $token;
        });
    }

    /**
     * Crea un ordine PayPal (intent: CAPTURE) e ritorna la risposta JSON come array.
     * $amountValue deve essere stringa con 2 decimali, es. "10.00".
     */
    public function createOrder(string $amountValue, string $currencyCode, string $returnUrl, string $cancelUrl, ?string $description = null, ?string $locale = null): array
    {
        $token = $this->getAccessToken();
        $url = $this->baseUrl() . '/v2/checkout/orders';

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($currencyCode ?: $this->currency),
                    'value' => $amountValue,
                ],
            ]],
            'application_context' => [
                'brand_name' => config('app.name'),
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'locale' => $locale ?: app()->getLocale(),
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
            ],
        ];
        if ($description) {
            $payload['purchase_units'][0]['description'] = $description;
        }
        $opts = [
            'connect_timeout' => 10,
            'timeout' => 25,
        ];
        if (\defined('CURLOPT_IPRESOLVE') && \defined('CURL_IPRESOLVE_V4')) {
            $opts['curl'] = [\CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4];
        }
        $response = Http::withOptions($opts)->withToken($token)->retry(2, 500)->post($url, $payload);
        if (!$response->successful()) {
            throw new \RuntimeException('PayPal create order failed: ' . $response->body());
        }
        return $response->json();
    }

    /**
     * Effettua la capture di un ordine (una volta che l'utente ha approvato su PayPal) e ritorna JSON array.
     */
    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();
        $url = $this->baseUrl() . '/v2/checkout/orders/' . urlencode($orderId) . '/capture';
        $opts = [
            'connect_timeout' => 10,
            'timeout' => 25,
        ];
        if (\defined('CURLOPT_IPRESOLVE') && \defined('CURL_IPRESOLVE_V4')) {
            $opts['curl'] = [\CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4];
        }
        $response = Http::withOptions($opts)->withToken($token)->retry(2, 500)->post($url, []);
        if (!$response->successful()) {
            throw new \RuntimeException('PayPal capture failed: ' . $response->body());
        }
        return $response->json();
    }

    /**
     * Estrae il link di approvazione (rel="approve") dalla risposta createOrder.
     */
    public static function getApproveLink(array $orderResponse): ?string
    {
        foreach (($orderResponse['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve' && !empty($link['href'])) {
                return (string) $link['href'];
            }
        }
        return null;
    }
}

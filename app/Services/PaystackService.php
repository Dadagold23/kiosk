<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ConsultancyRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    public function isConfigured(): bool
    {
        return filled(config('kiosk.payments.paystack.public_key'))
            && filled(config('kiosk.payments.paystack.secret_key'));
    }

    public function initializeTransaction(Payment $payment, array $metadata = []): array
    {
        $cancelAction = $this->cancelActionUrl($payment);

        $response = $this->client()->post('/transaction/initialize', [
            'email' => $payment->user?->email,
            'amount' => $this->amountInSubunit($payment->amount),
            'currency' => $payment->currency ?: config('kiosk.payments.currency', 'NGN'),
            'reference' => $payment->reference,
            'callback_url' => $this->callbackUrl(),
            'metadata' => array_merge([
                'payment_id' => $payment->id,
                'payable_type' => class_basename((string) $payment->payable_type),
                'payable_id' => $payment->payable_id,
                'cancel_action' => $cancelAction,
            ], $metadata),
        ]);

        $payload = $this->decodeResponse($response);

        return $payload['data'] ?? [];
    }

    public function verifyTransaction(string $reference): array
    {
        $response = $this->client()->get('/transaction/verify/' . urlencode($reference));
        $payload = $this->decodeResponse($response);

        return $payload['data'] ?? [];
    }

    public function hasValidSignature(string $payload, ?string $signature): bool
    {
        if (! $this->isConfigured() || blank($signature)) {
            return false;
        }

        $computed = hash_hmac('sha512', $payload, (string) config('kiosk.payments.paystack.secret_key'));

        return hash_equals($computed, (string) $signature);
    }

    public function acceptsCustomerEmail(?string $email): bool
    {
        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = strtolower((string) substr(strrchr($email, '@') ?: '', 1));

        if ($domain === '') {
            return false;
        }

        foreach (['.test', '.local', '.example', '.invalid'] as $suffix) {
            if (str_ends_with($domain, $suffix)) {
                return false;
            }
        }

        return ! in_array($domain, ['localhost', 'example.com', 'example.org', 'example.net'], true);
    }

    private function client()
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Paystack keys are not configured.');
        }

        $options = [
            'verify' => (bool) config('kiosk.payments.paystack.verify_ssl', true),
        ];

        if (config('kiosk.payments.paystack.force_ipv4', false) && defined('CURL_IPRESOLVE_V4')) {
            $options['curl'] = [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ];
        }

        return Http::baseUrl((string) config('kiosk.payments.paystack.base_url'))
            ->withToken((string) config('kiosk.payments.paystack.secret_key'))
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('kiosk.payments.paystack.timeout_seconds', 30))
            ->connectTimeout((int) config('kiosk.payments.paystack.connect_timeout_seconds', 10))
            ->withOptions($options);
    }

    private function decodeResponse(Response $response): array
    {
        $response->throw();

        $payload = $response->json();

        if (! is_array($payload) || ! ($payload['status'] ?? false)) {
            throw new RuntimeException((string) ($payload['message'] ?? 'Unable to process Paystack response.'));
        }

        return $payload;
    }

    private function amountInSubunit(float $amount): int
    {
        return (int) round($amount * 100);
    }

    private function callbackUrl(): string
    {
        $callbackRoute = (string) config('kiosk.payments.paystack.callback_route', 'payments.paystack.callback');

        if (filled($callbackRoute) && preg_match('#^https?://#i', $callbackRoute)) {
            return $callbackRoute;
        }

        $liveRouteUrl = route($callbackRoute, [], true);

        if (filled($liveRouteUrl) && str_starts_with($liveRouteUrl, 'http')) {
            return $liveRouteUrl;
        }

        $baseUrl = rtrim((string) config('kiosk.payments.paystack.public_app_url', config('app.url')), '/');
        $callbackPath = parse_url(route($callbackRoute, [], false), PHP_URL_PATH) ?: '/payments/paystack/callback';

        return $baseUrl . $callbackPath;
    }

    private function cancelActionUrl(Payment $payment): string
    {
        $payable = $payment->payable;

        if ($payable instanceof Order) {
            return route('orders.show', $payable);
        }

        if ($payable instanceof ServiceRequest) {
            return route('customer.services.show', $payable);
        }

        if ($payable instanceof ConsultancyRequest) {
            return route('customer.consultancy.show', $payable);
        }

        if ($payable instanceof Booking) {
            return route('customer.bookings.show', $payable);
        }

        return route('shop.index');
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.xendit.api_key');
        $this->baseUrl = config('services.xendit.base_url', 'https://api.xendit.co');
    }

    /**
     * Create a QRIS payment request.
     *
     * @param array $data Payment data
     * @return array|null
     */
    public function createQRISPayment(array $data): ?array
    {
        try {
            // Check if API key is set
            if (empty($this->apiKey)) {
                Log::error('Xendit API key is empty');
                return null;
            }

            $payload = [
                'reference_id' => $data['reference_id'],
                'type' => 'PAY',
                'country' => 'ID',
                'currency' => 'IDR',
                'request_amount' => (int) round($data['amount']),
                'channel_code' => 'QRIS',
                'channel_properties' => [],
                'description' => $data['description'] ?? 'Payment for Order',
                'metadata' => $data['metadata'] ?? [],
            ];

            // Add customer_name to channel_properties if provided
            if (!empty($data['customer_name'])) {
                $payload['channel_properties']['customer_name'] = $data['customer_name'];
            }

            Log::info('Sending Xendit QRIS request', [
                'url' => "{$this->baseUrl}/v3/payment_requests",
                'payload' => $payload,
            ]);

            $httpClient = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'api-version' => '2024-11-11',
                ])
                ->timeout(30);

            // For development: disable SSL verification if in local environment
            // WARNING: Only use this in development, never in production!
            if (app()->environment('local')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->post("{$this->baseUrl}/v3/payment_requests", $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Xendit QRIS response success', [
                    'status' => $response->status(),
                    'response_structure' => [
                        'keys' => array_keys($responseData),
                        'has_actions' => isset($responseData['actions']),
                        'actions_structure' => isset($responseData['actions']) ? array_keys($responseData['actions']) : null,
                    ],
                    'full_response' => $responseData,
                ]);
                return $responseData;
            }

            Log::info('Xendit QRIS response failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            Log::error('Xendit QRIS Payment Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'data' => $data,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return null;
        }
    }

    /**
     * Get payment request status.
     *
     * @param string $paymentRequestId
     * @return array|null
     */
    public function getPaymentRequest(string $paymentRequestId): ?array
    {
        try {
            $httpClient = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'api-version' => '2024-11-11',
                ]);

            // For development: disable SSL verification if in local environment
            if (app()->environment('local')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->get("{$this->baseUrl}/v3/payment_requests/{$paymentRequestId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit Get Payment Request Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payment_request_id' => $paymentRequestId,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit Get Payment Request Exception', [
                'message' => $e->getMessage(),
                'payment_request_id' => $paymentRequestId,
            ]);

            return null;
        }
    }

    /**
     * Verify webhook signature.
     *
     * @param string $signature
     * @param string $payload
     * @return bool
     */
    public function verifyWebhookSignature(string $signature, string $payload): bool
    {
        $webhookToken = config('services.xendit.webhook_token');
        
        if (!$webhookToken) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookToken);
        
        return hash_equals($expectedSignature, $signature);
    }
}


<?php

namespace App\Gateways;

use App\Interfaces\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class AsaasGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.base_url');
        $this->apiKey = config('services.asaas.api_key');
    }

    public function createSubscription(array $data): array
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/subscriptions", $data)
            ->throw()
            ->json();

        return [
            'external_subscription_id' => $response['id'],
            'external_customer_id' => $response['customer'],
            'status' => $this->normalizeStatus($response['status']),
        ];
    }

    public function charge(array $data): array
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/payments", $data)
            ->throw()
            ->json();

        return [
            'external_payment_id' => $response['id'],
            'status' => $this->normalizeStatus($response['status']),
        ];
    }

    public function cancelSubscription(string $externalId): bool
    {
        Http::withHeaders(['access_token' => $this->apiKey])
            ->delete("{$this->baseUrl}/subscriptions/{$externalId}")
            ->throw();

        return true;
    }

    public function refund(string $paymentId): bool
    {
        Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/payments/{$paymentId}/refund")
            ->throw();

        return true;
    }

    protected function normalizeStatus(string $asaasStatus): string
    {
        return match ($asaasStatus) {
            'ACTIVE' => 'active',
            'INACTIVE', 'CANCELLED' => 'cancelled',
            'PENDING' => 'pending',
            'OVERDUE' => 'overdue',
            'CONFIRMED', 'RECEIVED' => 'paid',
            default => strtolower($asaasStatus),
        };
    }
}

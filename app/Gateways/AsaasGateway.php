<?php

namespace App\Gateways;

use App\Dto\Asaas\CreateCustomerData;
use App\Dto\Asaas\CreateSubscriptionData;
use App\Dto\Asaas\CustomerResponse;
use App\Dto\Asaas\SubscriptionResponse;
use App\Enums\Gateways;
use App\Interfaces\Payments\PaymentGatewayInterface;
use App\Models\User;
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

    public function createCustomer(CreateCustomerData $data): CustomerResponse
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/customers", $data->toArray())
            ->throw()
            ->json();

        return CustomerResponse::fromArray($response);
    }

    public function createSubscription(CreateSubscriptionData $data): SubscriptionResponse
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/subscriptions", $data->toArray())
            ->throw()
            ->json();

        return SubscriptionResponse::fromArray($response);
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

    public function getOrCreateAsaasCustomer(User $user, CreateCustomerData $customerData): string
    {
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();

        if (! $subscription) {
            throw new \Exception('User has no active subscription context.');
        }

        $providerRecord = $subscription->subscriptionProviders()
            ->where('provider', Gateways::ASAAS)
            ->first();

        if ($providerRecord && $providerRecord->provider_customer_id) {
            return $providerRecord->provider_customer_id;
        }

        $asaasCustomer = $this->createCustomer($customerData);

        $subscription->subscriptionProviders()->updateOrCreate(
            ['provider' => 'asaas'],
            [
                'provider_customer_id' => $asaasCustomer->id,
                'status' => 'active',
            ]
        );

        return $asaasCustomer->id;
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

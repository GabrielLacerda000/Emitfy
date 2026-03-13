<?php

namespace App\Gateways;

use App\Dto\Payments\CreateCustomerData;
use App\Dto\Payments\CreateSubscriptionData;
use App\Dto\Payments\CreditCardTokenResponse;
use App\Dto\Payments\CustomerResponse;
use App\Dto\Payments\SubscriptionResponse;
use App\Dto\Payments\TokenizeCreditCardData;
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
        $payload = array_filter([
            'name'                 => $data->name,
            'cpfCnpj'              => $data->cpfCnpj,
            'email'                => $data->email,
            'phone'                => $data->phone,
            'mobilePhone'          => $data->mobilePhone,
            'address'              => $data->address,
            'addressNumber'        => $data->addressNumber,
            'complement'           => $data->complement,
            'province'             => $data->province,
            'postalCode'           => $data->postalCode,
            'externalReference'    => $data->externalReference,
            'notificationDisabled' => $data->notificationDisabled,
            'additionalEmails'     => $data->additionalEmails,
            'observations'         => $data->observations,
            'groupName'            => $data->groupName,
            'company'              => $data->company,
            'foreignCustomer'      => $data->foreignCustomer,
        ], fn ($v) => $v !== null);

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/customers", $payload)
            ->throw()
            ->json();

        return CustomerResponse::fromArray($response);
    }

    public function createSubscription(CreateSubscriptionData $data): SubscriptionResponse
    {
        $payload = [
            'customer' => $data->customer,
            'billingType' => $data->billingType->value,
            'value' => $data->value,
            'nextDueDate' => $data->nextDueDate,
            'cycle' => $data->cycle,
        ];

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/subscriptions", $payload)
            ->throw()
            ->json();

        return SubscriptionResponse::fromArray($response);
    }

    public function tokenizeCreditCard(TokenizeCreditCardData $data): CreditCardTokenResponse
    {
        $payload = [
            'customer'  => $data->customer,
            'remoteIp'  => $data->remoteIp,
            'creditCard' => [
                'holderName'  => $data->holderName,
                'number'      => $data->number,
                'expiryMonth' => $data->expiryMonth,
                'expiryYear'  => $data->expiryYear,
                'ccv'         => $data->ccv,
            ],
            'creditCardHolderInfo' => array_filter([
                'name'               => $data->holderInfoName,
                'email'              => $data->holderInfoEmail,
                'cpfCnpj'            => $data->holderInfoCpfCnpj,
                'postalCode'         => $data->holderInfoPostalCode,
                'addressNumber'      => $data->holderInfoAddressNumber,
                'addressComplement'  => $data->holderInfoAddressComplement,
                'phone'              => $data->holderInfoPhone,
                'mobilePhone'        => $data->holderInfoMobilePhone,
            ], fn ($v) => $v !== null),
        ];

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/creditCard/tokenizeCreditCard", $payload)
            ->throw()
            ->json();

        return CreditCardTokenResponse::fromArray($response);
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

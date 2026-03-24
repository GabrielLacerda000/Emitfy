<?php

namespace App\Gateways;

use App\Dto\Payments\ChargeData;
use App\Dto\Payments\ChargeResponse;
use App\Dto\Payments\CreateCustomerData;
use App\Dto\Payments\CreateSubscriptionData;
use App\Dto\Payments\CreditCardTokenResponse;
use App\Dto\Payments\CustomerResponse;
use App\Dto\Payments\SubscriptionResponse;
use App\Dto\Payments\TokenizeCreditCardData;
use App\Enums\PaymentStatus;
use App\Interfaces\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PaguedevGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.pague_dev.base_url');
        $this->apiKey = config('services.pague_dev.api_key');
    }

    public function charge(ChargeData $data): ChargeResponse
    {
        $payload = array_filter([
            'customer'          => $data->customer->toArray(),
            'amount'            => $data->amount,
            'description'       => $data->description,
            'externalReference' => $data->metadata['externalReference'] ?? null,
        ], fn ($v) => $v !== null);

        $response = Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->post("{$this->baseUrl}/pix", $payload)
            ->throw()
            ->json();

        return new ChargeResponse(
            externalPaymentId: $response['id'],
            status:            $response['status'],
            amount:            isset($response['amount']) ? (float) $response['amount'] : null,
            pixCode:           $response['pixCopyPaste'] ?? null,
            invoiceUrl:        null,
            expiresAt:         $response['expiresAt'] ?? null,
            qrCodeBase64:      $response['qrCodeBase64'] ?? null,
        );
    }

    public function mapStatus(string $status): PaymentStatus
    {
        return match ($status) {
            'payment_completed', 'CONFIRMED', 'RECEIVED', 'paid', 'completed' => PaymentStatus::PAID,
            'payment_expired', 'OVERDUE', 'overdue'                           => PaymentStatus::EXPIRED,
            'cancelled', 'CANCELLED', 'payment_cancelled'                    => PaymentStatus::FAILED,
            default                                                           => PaymentStatus::PENDING,
        };
    }

    public function createCustomer(CreateCustomerData $data): CustomerResponse
    {
        throw new \RuntimeException('PagueDev createCustomer not yet implemented.');
    }

    public function tokenizeCreditCard(TokenizeCreditCardData $data): CreditCardTokenResponse
    {
        throw new \RuntimeException('PagueDev tokenizeCreditCard not yet implemented.');
    }

    public function createSubscription(CreateSubscriptionData $data): SubscriptionResponse
    {
        throw new \RuntimeException('PagueDev createSubscription not yet implemented.');
    }

    public function cancelSubscription(string $externalId): bool
    {
        // TODO: implement when PagarDev API docs are available
        throw new \RuntimeException('PagarDev gateway not yet implemented.');
    }

    public function refund(string $paymentId): bool
    {
        // TODO: implement when PagarDev API docs are available
        throw new \RuntimeException('PagarDev gateway not yet implemented.');
    }
}

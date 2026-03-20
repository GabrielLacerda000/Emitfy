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
use App\Interfaces\Payments\PaymentGatewayInterface;

class PaguedevGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.pagar_dev.base_url');
        $this->apiKey = config('services.pagar_dev.api_key');
    }

    public function charge(ChargeData $data): ChargeResponse
    {
        // TODO: implement when PagarDev API docs are available
        throw new \RuntimeException('PagarDev gateway not yet implemented.');
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

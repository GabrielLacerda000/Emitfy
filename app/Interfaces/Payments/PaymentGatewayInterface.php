<?php

namespace App\Interfaces\Payments;

use App\Dto\Payments\ChargeData;
use App\Dto\Payments\ChargeResponse;
use App\Dto\Payments\CreateCustomerData;
use App\Dto\Payments\CreateSubscriptionData;
use App\Dto\Payments\CreditCardTokenResponse;
use App\Dto\Payments\CustomerResponse;
use App\Dto\Payments\SubscriptionResponse;
use App\Dto\Payments\TokenizeCreditCardData;

interface PaymentGatewayInterface
{
    /**
     * Create a customer on the gateway.
     */
    public function createCustomer(CreateCustomerData $data): CustomerResponse;

    /**
     * Tokenize a credit card on the gateway.
     */
    public function tokenizeCreditCard(TokenizeCreditCardData $data): CreditCardTokenResponse;

    /**
     * Create a subscription on the gateway.
     */
    public function createSubscription(CreateSubscriptionData $data): SubscriptionResponse;

    /**
     * Charge a one-off payment on the gateway.
     */
    public function charge(ChargeData $data): ChargeResponse;

    /**
     * Cancel a subscription on the gateway by its external ID.
     */
    public function cancelSubscription(string $externalId): bool;

    /**
     * Refund a payment on the gateway by its external payment ID.
     */
    public function refund(string $paymentId): bool;
}

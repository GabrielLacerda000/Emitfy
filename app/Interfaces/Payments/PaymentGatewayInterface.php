<?php

namespace App\Interfaces\Payments;

use App\Dto\Asaas\CreateSubscriptionData;
use App\Dto\Asaas\SubscriptionResponse;

interface PaymentGatewayInterface
{
    /**
     * Create a subscription on the gateway.
     */
    public function createSubscription(CreateSubscriptionData $data): SubscriptionResponse;

    /**
     * Charge a one-off payment on the gateway.
     * Returns normalized data: external_payment_id, status.
     */
    public function charge(array $data): array;

    /**
     * Cancel a subscription on the gateway by its external ID.
     */
    public function cancelSubscription(string $externalId): bool;

    /**
     * Refund a payment on the gateway by its external payment ID.
     */
    public function refund(string $paymentId): bool;
}

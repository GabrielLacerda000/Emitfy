<?php

namespace App\Interfaces\Payments;

interface PaymentGatewayInterface
{
    /**
     * Create a subscription on the gateway.
     * Returns normalized data: external_subscription_id, external_customer_id, status.
     */
    public function createSubscription(array $data): array;

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

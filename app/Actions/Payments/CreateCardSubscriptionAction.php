<?php

namespace App\Actions\Payments;

use App\Dto\Payments\CreateSubscriptionData;
use App\Enums\BillingType;
use App\Enums\SubscriptionStatus;
use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;

class CreateCardSubscriptionAction
{
    public function execute(Subscription $subscription): Subscription
    {
        $provider = $subscription->activeProvider;

        $gateway = PaymentGatewayFactory::make($provider->provider);

        $price = $subscription->billing_cycle === 'yearly'
            ? $subscription->plan->price_yearly
            : $subscription->plan->price_monthly;

        $data = new CreateSubscriptionData(
            customer: $provider->provider_customer_id,
            billingType: BillingType::CreditCard,
            value: (float) $price,
            nextDueDate: $subscription->billing_cycle === 'yearly'
                ? now()->addYear()->toDateString()
                : now()->addMonth()->toDateString(),
            cycle: strtoupper($subscription->billing_cycle) === 'YEARLY' ? 'YEARLY' : 'MONTHLY',
        );

        $response = $gateway->createSubscription($data);

        $provider->update([
            'provider_subscription_id' => $response->id,
            'provider_customer_id' => $response->customer,
            'status' => $this->normalizeStatus($response->status),
        ]);

        $subscription->update(['status' => $this->normalizeStatus($response->status)]);

        return $subscription->fresh(['activeProvider', 'plan']);
    }

    private function normalizeStatus(string $asaasStatus): SubscriptionStatus
    {
        return match ($asaasStatus) {
            'ACTIVE'              => SubscriptionStatus::ACTIVE,
            'INACTIVE','CANCELLED'=> SubscriptionStatus::CANCELLED,
            'CONFIRMED','RECEIVED'=> SubscriptionStatus::ACTIVE,
            'OVERDUE'             => SubscriptionStatus::OVERDUE,
            default               => SubscriptionStatus::PENDING,
        };
    }
}

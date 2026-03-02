<?php

namespace App\Actions\Payments;

use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;

class CreateSubscriptionAction
{
    public function execute(Subscription $subscription): Subscription
    {
        $provider = $subscription->activeProvider;

        $gateway = PaymentGatewayFactory::make($provider->provider);

        $price = $subscription->billing_cycle === 'yearly'
            ? $subscription->plan->price_yearly
            : $subscription->plan->price_monthly;

        $response = $gateway->createSubscription([
            'customer' => $provider->provider_customer_id,
            'value' => $price,
            'cycle' => strtoupper($subscription->billing_cycle) === 'YEARLY' ? 'YEARLY' : 'MONTHLY',
            'nextDueDate' => now()->addMonth()->toDateString(),
        ]);

        $provider->update([
            'provider_subscription_id' => $response['external_subscription_id'],
            'provider_customer_id' => $response['external_customer_id'],
            'status' => $response['status'],
        ]);

        $subscription->update(['status' => $response['status']]);

        return $subscription->fresh(['activeProvider', 'plan']);
    }
}

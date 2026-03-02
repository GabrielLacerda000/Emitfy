<?php

namespace App\Actions\Payments;

use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;

class ChargeSubscriptionAction
{
    public function execute(Subscription $subscription, float $amount): SubscriptionPayment
    {
        $provider = $subscription->activeProvider;

        $gateway = PaymentGatewayFactory::make($provider->provider);

        $response = $gateway->charge([
            'customer' => $provider->provider_customer_id,
            'value' => $amount,
            'dueDate' => now()->toDateString(),
        ]);

        return SubscriptionPayment::create([
            'subscription_id' => $subscription->id,
            'provider' => $provider->provider,
            'external_payment_id' => $response['external_payment_id'],
            'amount' => $amount,
            'status' => $response['status'],
            'paid_at' => $response['status'] === 'paid' ? now() : null,
            'raw_payload' => $response,
        ]);
    }
}

<?php

namespace App\Actions\Payments;

use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;

class CancelSubscriptionAction
{
    public function execute(Subscription $subscription): Subscription
    {
        $provider = $subscription->activeProvider;

        $gateway = PaymentGatewayFactory::make($provider->provider);

        $gateway->cancelSubscription($provider->provider_subscription_id);

        $provider->update(['status' => 'cancelled']);

        $subscription->update(['status' => 'cancelled']);

        return $subscription->fresh(['activeProvider']);
    }
}

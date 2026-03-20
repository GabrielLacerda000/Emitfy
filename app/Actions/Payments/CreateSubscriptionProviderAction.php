<?php

namespace App\Actions\Payments;

use App\Models\Subscription;
use App\Models\SubscriptionProvider;

class CreateSubscriptionProviderAction
{
    public function execute(Subscription $subscription, string $provider, ?string $providerCustomerId = null): SubscriptionProvider {
        return SubscriptionProvider::create([
            'subscription_id'      => $subscription->id,
            'provider'             => $provider,
            'provider_customer_id' => $providerCustomerId,
            'status'               => 'pending',
        ]);
    }
}

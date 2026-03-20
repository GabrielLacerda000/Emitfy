<?php

namespace App\Actions\Payments;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class CreateSubscriptionAction
{
    public function execute(User $user, Plan $plan, string $billingCycle = 'monthly'): Subscription
    {
        return Subscription::create([
            'user_id'       => $user->id,
            'plan_id'       => $plan->id,
            'billing_cycle' => $billingCycle,
            'status'        => SubscriptionStatus::PENDING,
        ]);
    }
}

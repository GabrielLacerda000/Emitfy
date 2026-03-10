<?php

use App\Actions\Payments\CancelSubscriptionAction;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionProvider;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('cancels subscription via asaas gateway and marks as cancelled', function () {
    Http::fake([
        '*/subscriptions/*' => Http::response([], 200),
    ]);

    $plan = Plan::factory()->create();
    $user = User::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'billing_cycle' => 'monthly',
    ]);

    SubscriptionProvider::factory()->create([
        'subscription_id' => $subscription->id,
        'provider' => 'asaas',
        'provider_customer_id' => 'cus_asaas_456',
        'provider_subscription_id' => 'sub_asaas_123',
        'status' => 'active',
    ]);

    $result = app(CancelSubscriptionAction::class)->execute($subscription);

    expect($result->status)->toBe('cancelled');
    expect($result->activeProvider->status)->toBe('cancelled');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'sub_asaas_123')
            && $request->method() === 'DELETE';
    });
})->skip('integração com gateway ainda não implementada.');

<?php

use App\Actions\Payments\CreateSubscriptionAction;
use App\Factories\PaymentGatewayFactory;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionProvider;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('creates subscription via asaas gateway and updates provider record', function () {
    Http::fake([
        '*/subscriptions' => Http::response([
            'id' => 'sub_asaas_123',
            'customer' => 'cus_asaas_456',
            'status' => 'ACTIVE',
        ], 200),
    ]);

    $plan = Plan::factory()->create([
        'price_monthly' => 49.90,
        'price_yearly' => 499.00,
    ]);

    $user = User::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'pending',
        'billing_cycle' => 'monthly',
    ]);

    $provider = SubscriptionProvider::factory()->create([
        'subscription_id' => $subscription->id,
        'provider' => 'asaas',
        'provider_customer_id' => 'cus_asaas_456',
        'provider_subscription_id' => null,
        'status' => 'pending',
    ]);

    $result = app(CreateSubscriptionAction::class)->execute($subscription);

    expect($result->status)->toBe('active');
    expect($result->activeProvider->provider_subscription_id)->toBe('sub_asaas_123');
    expect($result->activeProvider->status)->toBe('active');
});

test('creates subscription with yearly billing cycle', function () {
    Http::fake([
        '*/subscriptions' => Http::response([
            'id' => 'sub_asaas_yearly_789',
            'customer' => 'cus_asaas_456',
            'status' => 'ACTIVE',
        ], 200),
    ]);

    $plan = Plan::factory()->create([
        'price_monthly' => 49.90,
        'price_yearly' => 499.00,
    ]);

    $user = User::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'pending',
        'billing_cycle' => 'yearly',
    ]);

    SubscriptionProvider::factory()->create([
        'subscription_id' => $subscription->id,
        'provider' => 'asaas',
        'provider_customer_id' => 'cus_asaas_456',
        'provider_subscription_id' => null,
        'status' => 'pending',
    ]);

    $result = app(CreateSubscriptionAction::class)->execute($subscription);

    Http::assertSent(function ($request) {
        return $request['value'] == 499.00
            && $request['cycle'] === 'YEARLY';
    });

    expect($result->status)->toBe('active');
});

test('factory throws for unsupported provider', function () {
    PaymentGatewayFactory::make('unsupported_provider');
})->throws(InvalidArgumentException::class);

<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionProvider>
 */
class SubscriptionProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'provider' => $this->faker->randomElement(['asaas', 'pagar_dev']),
            'provider_customer_id' => 'cus_'.$this->faker->uuid(),
            'provider_subscription_id' => 'sub_'.$this->faker->uuid(),
            'last_provider_payment_id' => null,
            'status' => $this->faker->randomElement(['active', 'pending', 'cancelled']),
            'metadata' => null,
        ];
    }
}

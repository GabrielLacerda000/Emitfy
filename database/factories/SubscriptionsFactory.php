<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriptions>
 */
class SubscriptionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 100),
            'plan' => $this->faker->randomElement(['free', 'pro', 'business']),
            'provider' => $this->faker->randomElement(['stripe']),
            'provider_subscription_id' => $this->faker->uuid,
            'status' => $this->faker->randomElement(['active', 'canceled', 'past_due']),
            'current_period_end' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

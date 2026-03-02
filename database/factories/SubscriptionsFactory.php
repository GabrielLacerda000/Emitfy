<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
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
            'user_id' => \App\Models\User::factory(),
            'plan_id' => \App\Models\Plan::factory(),
            'status' => $this->faker->randomElement(['active', 'canceled', 'past_due']),
            'current_period_end' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

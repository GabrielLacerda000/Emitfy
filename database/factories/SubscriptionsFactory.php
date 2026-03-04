<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionsFactory extends Factory
{
    protected $model = Subscription::class;

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
            'billing_cycle' => $this->faker->randomElement(['monthly', 'yearly']),
            'current_period_end' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payments>
 */
class PaymentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => $this->faker->numberBetween(1, 100),
            'provider' => $this->faker->randomElement(['stripe', 'paypal']),
            'provider_payment_id' => $this->faker->uuid,
            'amount' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'paid_at' => $this->faker->dateTimeThisYear,
        ];
    }
}

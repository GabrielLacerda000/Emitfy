<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPayment>
 */
class SubscriptionPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'provider' => $this->faker->randomElement(['asaas', 'pagar_dev']),
            'external_payment_id' => 'pay_'.$this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['paid', 'pending', 'failed']),
            'paid_at' => null,
            'raw_payload' => null,
        ];
    }
}

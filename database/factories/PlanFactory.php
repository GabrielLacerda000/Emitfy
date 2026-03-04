<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Free', 'Pro', 'Business']),
            'price_monthly' => $this->faker->randomFloat(2, 0, 199),
            'price_yearly' => $this->faker->randomFloat(2, 0, 1990),
            'max_clients' => $this->faker->randomElement([10, 50, 500]),
            'max_invoices' => $this->faker->randomElement([20, 100, 1000]),
        ];
    }
}

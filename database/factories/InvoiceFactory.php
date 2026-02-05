<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use App\database\factories\ClientFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => ClientFactory::new(),
            'number' => fake()->unique()->bothify('INV-####'),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue']),
            'issue_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'due_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'subtotal' => fake()->randomFloat(2, 100, 1000),
            'tax' => fake()->randomFloat(2, 0, 10),
            'total' => fake()->randomFloat(2, 100, 1000),
            'notes' => fake()->text,
            'public_token' => fake()->uuid(),
        ];
    }
}

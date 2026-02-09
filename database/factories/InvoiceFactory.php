<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'status' => fake()->randomElement(InvoiceStatus::cases())->value,
            'issue_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'due_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'subtotal' => fake()->randomFloat(2, 100, 1000),
            'tax' => fake()->randomFloat(2, 0, 10),
            'total' => fake()->randomFloat(2, 100, 1000),
            'notes' => fake()->text,
            'public_token' => fake()->uuid(),
        ];
    }

    /**
     * Indicate that the invoice is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::DRAFT->value,
            'sent_at' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the invoice has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::SENT->value,
            'sent_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the invoice has been paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::PAID->value,
            'sent_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
            'paid_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the invoice is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::OVERDUE->value,
            'issue_date' => fake()->dateTimeBetween('-2 months', '-1 month'),
            'due_date' => fake()->dateTimeBetween('-1 month', '-1 day'),
            'sent_at' => fake()->dateTimeBetween('-2 months', '-1 month'),
            'paid_at' => null,
        ]);
    }
}

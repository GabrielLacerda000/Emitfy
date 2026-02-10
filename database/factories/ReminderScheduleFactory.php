<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReminderSchedule>
 */
class ReminderScheduleFactory extends Factory
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
            'type' => $this->faker->randomElement(['before_due', 'on_due', 'after_due']),
            'offset_days' => $this->faker->numberBetween(-30, 30),
            'sent_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

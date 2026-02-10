<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Collection;

class CreateReminderSchedulesAction
{
    /**
     * Create reminder schedules for an invoice.
     *
     * Creates 3 reminders:
     * - before_due: 3 days before due date (-3 offset)
     * - on_due: on the due date (0 offset)
     * - after_due: 7 days after due date (+7 offset)
     *
     * @return Collection<ReminderSchedule>
     */
    public function __invoke(Invoice $invoice): Collection
    {
        $schedules = [
            ['type' => 'before_due', 'offset_days' => -3],
            ['type' => 'on_due', 'offset_days' => 0],
            ['type' => 'after_due', 'offset_days' => 7],
        ];

        return collect($schedules)->map(function ($schedule) use ($invoice) {
            return $invoice->reminderSchedules()->create($schedule);
        });
    }
}

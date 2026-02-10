<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Jobs\SendInvoiceReminderJob;
use App\Models\ReminderSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send {--dry-run : List pending reminders without sending} {--date= : Override date for testing (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled invoice payment reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $date = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : today();

        $queued = 0;
        $failed = 0;

        if ($dryRun) {
            $this->info("DRY RUN MODE - No reminders will be sent");
        }

        $this->info("Checking reminders for date: {$date->format('Y-m-d')}");

        ReminderSchedule::query()
            ->whereNull('sent_at')
            ->whereHas('invoice', function ($query) {
                $query->whereIn('status', [InvoiceStatus::SENT, InvoiceStatus::OVERDUE]);
            })
            ->whereRaw("date(
                (SELECT due_date FROM invoices WHERE id = reminder_schedules.invoice_id),
                offset_days || ' days'
            ) = ?", [$date->format('Y-m-d')])
            ->with(['invoice.client', 'invoice.user'])
            ->chunk(100, function ($reminders) use (&$queued, &$failed, $dryRun) {
                foreach ($reminders as $reminder) {
                    if ($dryRun) {
                        $this->line("Would send: Invoice #{$reminder->invoice->number} - {$reminder->type} (offset: {$reminder->offset_days} days)");
                        $queued++;
                    } else {
                        try {
                            SendInvoiceReminderJob::dispatch($reminder);
                            $queued++;
                        } catch (\Throwable $e) {
                            $failed++;
                            Log::error('Failed to dispatch reminder job', [
                                'reminder_id' => $reminder->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            });

        if ($dryRun) {
            $this->info("Found {$queued} pending reminder(s).");
        } else {
            $this->info("Queued {$queued} reminder(s), {$failed} failed.");
        }

        return Command::SUCCESS;
    }
}

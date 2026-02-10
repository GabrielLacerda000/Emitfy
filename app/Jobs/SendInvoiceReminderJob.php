<?php

namespace App\Jobs;

use App\Enums\InvoiceStatus;
use App\Mail\InvoiceReminderMail;
use App\Models\ReminderSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reminderId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Query reminder with relationships
        $reminder = ReminderSchedule::with('invoice.client', 'invoice.user')
            ->find($this->reminderId);

        // Skip if reminder not found or deleted
        if (! $reminder) {
            return;
        }

        // Skip if already sent or deleted
        if ($reminder->sent_at) {
            return;
        }

        // Verify invoice is still SENT or OVERDUE (defensive check)
        $invoice = $reminder->invoice;
        if (! in_array($invoice->status, [InvoiceStatus::SENT, InvoiceStatus::OVERDUE])) {
            return;
        }

        // Send email with PDF attachment
        Mail::to($invoice->client->email)
            ->send((new InvoiceReminderMail($reminder))->withPdf());

        // Mark as sent
        $reminder->update(['sent_at' => now()]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send invoice reminder email', [
            'reminder_id' => $this->reminderId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}

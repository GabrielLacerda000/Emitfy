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
        public ReminderSchedule $reminder,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Skip if already sent or deleted
        if (! $this->reminder || $this->reminder->sent_at) {
            return;
        }

        // Load invoice with relationships if not already loaded
        $this->reminder->loadMissing('invoice.client', 'invoice.user');

        // Verify invoice is still SENT or OVERDUE (defensive check)
        $invoice = $this->reminder->invoice;
        if (! in_array($invoice->status, [InvoiceStatus::SENT, InvoiceStatus::OVERDUE])) {
            return;
        }

        // Send email with PDF attachment
        Mail::to($invoice->client->email)
            ->send((new InvoiceReminderMail($this->reminder))->withPdf());

        // Mark as sent
        $this->reminder->update(['sent_at' => now()]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send invoice reminder email', [
            'reminder_id' => $this->reminder->id,
            'invoice_id' => $this->reminder->invoice_id,
            'type' => $this->reminder->type,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}

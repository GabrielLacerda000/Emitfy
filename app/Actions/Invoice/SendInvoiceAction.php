<?php

namespace App\Actions\Invoice;

use App\Enums\InvoiceStatus;
use App\Jobs\SendInvoiceEmailJob;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class SendInvoiceAction
{
    public function __construct(
        protected CreateReminderSchedulesAction $createReminderSchedules,
    ) {}

    /**
     * Send an invoice via email.
     *
     * @throws \Exception
     */
    public function __invoke(Invoice $invoice): bool
    {
        if (! $this->canBeSent($invoice)) {
            throw new \Exception('Invoice cannot be sent. Paid invoices cannot be resent.');
        }

        DB::transaction(function () use ($invoice) {
            if ($invoice->status === InvoiceStatus::DRAFT) {
                $invoice->markAsSent();

                // Create reminder schedules for newly sent invoice
                ($this->createReminderSchedules)($invoice);
            } else {
                // For SENT/OVERDUE invoices, just update sent_at timestamp
                $invoice->update(['sent_at' => now()]);
            }

            // Dispatch email job
            SendInvoiceEmailJob::dispatch($invoice);
        });

        return true;
    }

    /**
     * Check if invoice can be sent.
     */
    private function canBeSent(Invoice $invoice): bool
    {
        // PAID invoices cannot be sent
        return $invoice->status !== InvoiceStatus::PAID;
    }
}

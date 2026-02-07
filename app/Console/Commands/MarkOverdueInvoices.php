<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark sent invoices past their due date as overdue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = 0;

        Invoice::query()
            ->where('status', InvoiceStatus::SENT)
            ->where('due_date', '<', today())
            ->chunk(100, function ($invoices) use (&$count) {
                foreach ($invoices as $invoice) {
                    if ($invoice->markAsOverdue()) {
                        $count++;
                    }
                }
            });

        $this->info("Marked {$count} invoice(s) as overdue.");

        return Command::SUCCESS;
    }
}

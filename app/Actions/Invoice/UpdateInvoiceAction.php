<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class UpdateInvoiceAction
{
    public function __construct(
        protected CalculateInvoiceTotalsAction $calculateTotals,
        protected SyncInvoiceItemsAction $syncItems,
    ) {}

    public function __invoke(Invoice $invoice, array $validated): Invoice
    {
        return DB::transaction(function () use ($invoice, $validated) {
            // Calculate new totals
            $totals = ($this->calculateTotals)($validated['items'], $validated['tax']);

            // Update the invoice (excluding invoice number which should never change)
            $invoice->update([
                'client_id' => $validated['client_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'subtotal' => $totals['subtotal'],
                'tax' => $validated['tax'],
                'total' => $totals['total'],
                'notes' => $validated['notes'] ?? null,
                'paid_at' => $validated['paid_at'] ?? null,
            ]);

            // Sync invoice items
            ($this->syncItems)($invoice, $validated['items']);

            return $invoice->fresh();
        });
    }
}

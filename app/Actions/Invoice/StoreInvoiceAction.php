<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\InvoiceStatus;

class StoreInvoiceAction
{
    public function __construct(
        protected GenerateInvoiceNumberAction $generateInvoiceNumber,
        protected GeneratePublicTokenAction $generatePublicToken,
        protected CalculateInvoiceTotalsAction $calculateTotals,
        protected PrepareInvoiceItemsAction $prepareItems,
        protected SendInvoiceAction $sendInvoice,
    ) {}

    public function __invoke(User $user, array $validated): Invoice
    {
        return DB::transaction(function () use ($user, $validated) {
            // Generate invoice number and public token
            $invoiceNumber = ($this->generateInvoiceNumber)($user, $validated['issue_date']);
            $publicToken = ($this->generatePublicToken)();

            // Calculate totals
            $totals = ($this->calculateTotals)($validated['items'], $validated['tax']);

            // Create the invoice
            $invoice = $user->invoices()->create([
                'client_id' => $validated['client_id'],
                'number' => $invoiceNumber,
                'public_token' => $publicToken,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'subtotal' => $totals['subtotal'],
                'tax' => $validated['tax'],
                'total' => $totals['total'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Prepare and create invoice items
            $preparedItems = ($this->prepareItems)($validated['items']);
            $invoice->items()->createMany($preparedItems);

            // Send the invoice if status is not DRAFT
            if ($invoice->status !== InvoiceStatus::DRAFT) {
                ($this->sendInvoice)($invoice);
            }

            return $invoice;
        });
    }
}

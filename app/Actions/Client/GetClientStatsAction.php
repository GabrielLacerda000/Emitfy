<?php

namespace App\Actions\Client;

use App\Enums\InvoiceStatus;
use App\Models\Client;

class GetClientStatsAction
{
    public function execute(Client $client): array
    {
        // Calculate total paid stats (amount + count)
        $paidStats = $client->invoices()
            ->where('status', InvoiceStatus::PAID)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Calculate total pending stats (SENT invoices)
        $pendingStats = $client->invoices()
            ->where('status', InvoiceStatus::SENT)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Calculate total overdue stats (amount + count)
        $overdueStats = $client->invoices()
            ->where('status', InvoiceStatus::OVERDUE)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Calculate total draft stats (amount + count)
        $draftStats = $client->invoices()
            ->where('status', InvoiceStatus::DRAFT)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Get the most recent sent invoice
        $lastInvoiceSent = $client->invoices()
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->first();

        return [
            'totalPaid' => number_format($paidStats->total ?? 0, 2, '.', ''),
            'totalPaidCount' => $paidStats->count ?? 0,
            'totalPending' => number_format($pendingStats->total ?? 0, 2, '.', ''),
            'totalPendingCount' => $pendingStats->count ?? 0,
            'totalOverdue' => number_format($overdueStats->total ?? 0, 2, '.', ''),
            'totalOverdueCount' => $overdueStats->count ?? 0,
            'totalDraft' => number_format($draftStats->total ?? 0, 2, '.', ''),
            'totalDraftCount' => $draftStats->count ?? 0,
            'lastInvoiceSent' => $lastInvoiceSent,
        ];
    }
}

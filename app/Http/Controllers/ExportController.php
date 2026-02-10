<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export invoices to CSV format.
     */
    public function invoicesCsv(Request $request): StreamedResponse
    {
        $query = $request->user()->invoices()
            ->with('client:id,name,email,company_name');

        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply date range filters if provided
        if ($request->has('date_from')) {
            $query->where('issue_date', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('issue_date', '<=', $request->input('date_to'));
        }

        $user = $request->user();

        return response()->stream(function () use ($query, $user) {
            $handle = fopen('php://output', 'w');

            // Write CSV header row
            fputcsv($handle, [
                'Number',
                'Client Name',
                'Client Email',
                'Issue Date',
                'Due Date',
                'Status',
                'Subtotal',
                'Tax',
                'Total',
                'Currency',
                'Sent At',
                'Paid At',
            ]);

            // Write data rows with chunking for memory efficiency
            $query->chunk(1000, function ($invoices) use ($handle, $user) {
                foreach ($invoices as $invoice) {
                    fputcsv($handle, [
                        $invoice->number,
                        $invoice->client->name,
                        $invoice->client->email,
                        $invoice->issue_date->format('Y-m-d'),
                        $invoice->due_date->format('Y-m-d'),
                        $invoice->status->value,
                        number_format($invoice->subtotal, 2, '.', ''),
                        number_format($invoice->tax, 2, '.', ''),
                        number_format($invoice->total, 2, '.', ''),
                        $user->currency,
                        $invoice->sent_at?->format('Y-m-d H:i:s') ?? '',
                        $invoice->paid_at?->format('Y-m-d H:i:s') ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invoices-'.date('Y-m-d').'.csv"',
        ]);
    }
}

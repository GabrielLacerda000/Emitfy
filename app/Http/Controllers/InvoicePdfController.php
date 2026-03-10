<?php

namespace App\Http\Controllers;

use App\Gates\InvoiceGate;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoicePdfController extends Controller
{
    /**
     * Generate and return invoice PDF.
     */
    public function show(Request $request, Invoice $invoice): Response
    {
        // Authorization check
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! InvoiceGate::canViewPdf($request->user())) {
            abort(403, 'Upgrade required to access invoice PDFs.');
        }

        // Load relationships
        $invoice->load('client', 'items');

        // Generate PDF
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'user' => $request->user(),
        ]);

        // Set paper size
        $pdf->setPaper('A4', 'portrait');

        // Determine mode (download by default, stream if requested)
        $mode = $request->query('mode', 'download');
        $filename = "invoice-{$invoice->number}.pdf";

        if ($mode === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}

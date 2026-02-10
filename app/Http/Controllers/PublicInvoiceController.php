<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Inertia\Inertia;
use Inertia\Response;

class PublicInvoiceController extends Controller
{
    /**
     * Display the invoice for public viewing.
     */
    public function show(string $public_token): Response
    {
        $invoice = Invoice::where('public_token', $public_token)
            ->with(['client', 'items', 'user'])
            ->firstOrFail();

        if ($invoice->isDraft()) {
            abort(403, 'This invoice has not been sent yet.');
        }

        return Inertia::render('invoices/Public', [
            'invoice' => $invoice,
        ]);
    }
}

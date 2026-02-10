<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\SendInvoiceAction;
use App\Actions\Invoice\StoreInvoiceAction;
use App\Actions\Invoice\UpdateInvoiceAction;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected StoreInvoiceAction $storeInvoice,
        protected UpdateInvoiceAction $updateInvoice,
        protected SendInvoiceAction $sendInvoice,
    ) {}

    /**
     * Display a listing of the user's invoices.
     */
    public function index(Request $request): Response
    {
        $query = $request->user()->invoices()
            ->with('client:id,name,email,company_name');

        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        return Inertia::render('invoices/Index', [
            'invoices' => $query->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('invoices/Create', [
            'clients' => $request->user()->clients()->get(['id', 'name', 'email', 'company_name']),
        ]);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        ($this->storeInvoice)($request->user(), $request->validated());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, Invoice $invoice): Response
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        $invoice->load('client', 'items');

        return Inertia::render('invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Request $request, Invoice $invoice): Response
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        $invoice->load('client', 'items');

        return Inertia::render('invoices/Edit', [
            'invoice' => $invoice,
            'clients' => $request->user()->clients()->get(['id', 'name', 'email', 'company_name']),
        ]);
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        ($this->updateInvoice)($invoice, $request->validated());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Send the invoice via email.
     */
    public function send(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        try {
            // Check if this is a resend BEFORE sending
            $isResend = $invoice->sent_at !== null;

            ($this->sendInvoice)($invoice);

            $message = $isResend
                ? 'Invoice resent successfully.'
                : 'Invoice sent successfully.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

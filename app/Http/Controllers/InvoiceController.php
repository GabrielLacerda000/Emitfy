<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\CalculateInvoiceTotalsAction;
use App\Actions\Invoice\GenerateInvoiceNumberAction;
use App\Actions\Invoice\GeneratePublicTokenAction;
use App\Actions\Invoice\PrepareInvoiceItemsAction;
use App\Actions\Invoice\SyncInvoiceItemsAction;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected GenerateInvoiceNumberAction $generateInvoiceNumber,
        protected GeneratePublicTokenAction $generatePublicToken,
        protected CalculateInvoiceTotalsAction $calculateTotals,
        protected PrepareInvoiceItemsAction $prepareItems,
        protected SyncInvoiceItemsAction $syncItems,
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
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {
            // Generate invoice number and public token
            $invoiceNumber = ($this->generateInvoiceNumber)($request->user(), $validated['issue_date']);
            $publicToken = ($this->generatePublicToken)();

            // Calculate totals
            $totals = ($this->calculateTotals)($validated['items'], $validated['tax']);

            // Create invoice
            $invoice = $request->user()->invoices()->create([
                'client_id' => $validated['client_id'],
                'number' => $invoiceNumber,
                'status' => $validated['status'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $totals['subtotal'],
                'tax' => $validated['tax'],
                'total' => $totals['total'],
                'notes' => $validated['notes'] ?? null,
                'public_token' => $publicToken,
            ]);

            // Prepare and create items
            $preparedItems = ($this->prepareItems)($validated['items']);
            $invoice->items()->createMany($preparedItems);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
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

        $validated = $request->validated();

        DB::transaction(function () use ($invoice, $validated) {
            // Calculate new totals
            $totals = ($this->calculateTotals)($validated['items'], $validated['tax']);

            // Update invoice data (not including invoice number)
            $invoice->update([
                'client_id' => $validated['client_id'],
                'status' => $validated['status'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $totals['subtotal'],
                'tax' => $validated['tax'],
                'total' => $totals['total'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Sync items
            ($this->syncItems)($invoice, $validated['items']);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
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
}

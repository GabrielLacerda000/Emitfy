<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        :root {
            --color-gray-50: #f9fafb;
            --color-gray-100: #f3f4f6;
            --color-gray-200: #e5e7eb;
            --color-gray-300: #d1d5db;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-600: #4b5563;
            --color-gray-700: #374151;
            --color-gray-800: #1f2937;
            --color-gray-900: #111827;
            --color-blue-50: #eff6ff;
            --color-blue-600: #2563eb;
            --color-green-50: #f0fdf4;
            --color-green-600: #16a34a;
            --color-red-50: #fef2f2;
            --color-red-600: #dc2626;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: var(--color-gray-900);
            line-height: 1.5;
        }

        .container {
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-size: 24pt;
            font-weight: bold;
            color: var(--color-gray-900);
            margin-bottom: 30px;
        }

        h2 {
            font-size: 12pt;
            font-weight: bold;
            color: var(--color-gray-700);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header {
            margin-bottom: 30px;
        }

        .header-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .header-grid td {
            vertical-align: top;
            padding: 0;
        }

        .header-grid td:first-child {
            width: 50%;
        }

        .header-grid td:last-child {
            width: 50%;
            text-align: right;
        }

        .metadata-table {
            width: 100%;
            margin-top: 10px;
        }

        .metadata-table td {
            padding: 3px 0;
        }

        .metadata-table td:first-child {
            color: var(--color-gray-600);
            font-weight: bold;
            width: 40%;
        }

        .metadata-table td:last-child {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-draft {
            background-color: var(--color-gray-100);
            color: var(--color-gray-700);
        }

        .badge-sent {
            background-color: var(--color-blue-50);
            color: var(--color-blue-600);
        }

        .badge-paid {
            background-color: var(--color-green-50);
            color: var(--color-green-600);
        }

        .badge-overdue {
            background-color: var(--color-red-50);
            color: var(--color-red-600);
        }

        .section {
            margin-bottom: 30px;
        }

        .client-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .client-grid td {
            vertical-align: top;
            padding: 0;
        }

        .client-grid td:first-child {
            width: 50%;
            padding-right: 20px;
        }

        .client-grid td:last-child {
            width: 50%;
        }

        .client-info {
            line-height: 1.6;
        }

        .client-info p {
            margin: 2px 0;
        }

        .client-name {
            font-weight: bold;
            font-size: 11pt;
            color: var(--color-gray-900);
        }

        .client-company {
            color: var(--color-gray-600);
            font-size: 10pt;
        }

        .client-email {
            color: var(--color-gray-600);
            font-size: 9pt;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table thead {
            background-color: var(--color-gray-100);
        }

        .items-table th {
            text-align: left;
            padding: 10px;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--color-gray-700);
            border-bottom: 2px solid var(--color-gray-300);
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid var(--color-gray-200);
        }

        .items-table tbody td {
            padding: 12px 10px;
            color: var(--color-gray-900);
        }

        .items-table tbody td.text-right {
            text-align: right;
        }

        .items-table tbody td.description {
            font-weight: 500;
        }

        .totals-container {
            width: 100%;
            margin-top: 20px;
        }

        .totals-table {
            float: right;
            width: 300px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
        }

        .totals-table td:first-child {
            text-align: right;
            padding-right: 30px;
            color: var(--color-gray-600);
            font-weight: bold;
        }

        .totals-table td:last-child {
            text-align: right;
            color: var(--color-gray-900);
        }

        .totals-table .total-row {
            border-top: 2px solid var(--color-gray-300);
            margin-top: 10px;
        }

        .totals-table .total-row td {
            padding-top: 15px;
            font-size: 12pt;
            font-weight: bold;
        }

        .notes {
            background-color: var(--color-gray-50);
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid var(--color-gray-300);
            margin-top: 20px;
        }

        .notes p {
            color: var(--color-gray-700);
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .payment-instructions {
            background-color: var(--color-gray-50);
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
            border: 1px solid var(--color-gray-200);
        }

        .payment-instructions h3 {
            font-size: 11pt;
            font-weight: bold;
            color: var(--color-gray-900);
            margin-bottom: 10px;
        }

        .payment-instructions p {
            color: var(--color-gray-700);
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .payment-url {
            color: var(--color-blue-600);
            word-wrap: break-word;
            font-size: 9pt;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Section --}}
        <div class="header">
            <h1>INVOICE</h1>

            <table class="header-grid">
                <tr>
                    <td>
                        <h2>From</h2>
                        <div class="client-info">
                            <p class="client-name">{{ $user->name }}</p>
                            <p class="client-email">{{ $user->email }}</p>
                        </div>
                    </td>
                    <td>
                        <table class="metadata-table">
                            <tr>
                                <td>Invoice Number:</td>
                                <td>{{ $invoice->number }}</td>
                            </tr>
                            <tr>
                                <td>Issue Date:</td>
                                <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td>Due Date:</td>
                                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td>Status:</td>
                                <td>
                                    <span class="badge badge-{{ $invoice->status->value }}">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Client Section --}}
        <div class="section">
            <h2>Bill To</h2>
            <div class="client-info">
                <p class="client-name">{{ $invoice->client->name }}</p>
                @if($invoice->client->company_name)
                    <p class="client-company">{{ $invoice->client->company_name }}</p>
                @endif
                <p class="client-email">{{ $invoice->client->email }}</p>
            </div>
        </div>

        {{-- Line Items Table --}}
        <div class="section">
            <h2>Items</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="description">{{ $item->description }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">{{ App\Helpers\CurrencyHelper::formatNumber($item->unit_price, $user->currency) }}</td>
                            <td class="text-right">{{ App\Helpers\CurrencyHelper::formatNumber($item->total, $user->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals Section --}}
        <div class="section clearfix">
            <div class="totals-container">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td>{{ App\Helpers\CurrencyHelper::format($invoice->subtotal, $user->currency) }}</td>
                    </tr>
                    <tr>
                        <td>Tax:</td>
                        <td>{{ App\Helpers\CurrencyHelper::format($invoice->tax, $user->currency) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total:</td>
                        <td>{{ App\Helpers\CurrencyHelper::format($invoice->total, $user->currency) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Notes Section (conditional) --}}
        @if($invoice->notes)
            <div class="section">
                <h2>Notes</h2>
                <div class="notes">
                    <p>{{ $invoice->notes }}</p>
                </div>
            </div>
        @endif

        {{-- Payment Instructions Section --}}
        <div class="payment-instructions">
            <h3>Payment Instructions</h3>
            <p>To view and pay this invoice online, please visit:</p>
            <p class="payment-url">{{ config('app.url') }}/i/{{ $invoice->public_token }}</p>
        </div>
    </div>
</body>
</html>

<x-mail::message>
# Invoice {{ $invoice->number }}

Hello {{ $invoice->client->name }},

You've received a new invoice from **{{ $user->name }}**.

## Invoice Details

- **Invoice Number:** {{ $invoice->number }}
- **Issue Date:** {{ $invoice->issue_date->format('M d, Y') }}
- **Due Date:** {{ $invoice->due_date->format('M d, Y') }}
- **Status:** {{ $invoice->status->label() }}
- **Total Amount:** {{ App\Helpers\CurrencyHelper::format($invoice->total, $user->currency) }}

<x-mail::button :url="config('app.url') . '/i/' . $invoice->public_token">
View Invoice
</x-mail::button>

## Payment Instructions

Please view the full invoice online to see itemized details and payment options. Payment is due by {{ $invoice->due_date->format('F d, Y') }}.

If you have any questions, please reply to this email.

Thank you,<br>
{{ $user->name }}
</x-mail::message>

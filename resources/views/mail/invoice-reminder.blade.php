<x-mail::message>
# Payment Reminder - Invoice {{ $invoice->number }}

Hello {{ $invoice->client->name }},

{{-- $daysContext and $reminder are passed directly from the mailable --}}

@if($reminder->type === 'before_due')
This is a friendly reminder that you have an upcoming payment due for Invoice **{{ $invoice->number }}** from **{{ $user->name }}**.

@if($daysContext['is_today'])
Payment is due **today**.
@else
Payment is due in **{{ $daysContext['days'] }} {{ Str::plural('day', $daysContext['days']) }}**.
@endif
@endif

@if($reminder->type === 'on_due')
Payment for Invoice **{{ $invoice->number }}** from **{{ $user->name }}** is **due today**.

We kindly request that you process this payment at your earliest convenience to avoid any late fees or service interruptions.
@endif

@if($reminder->type === 'after_due')
This is an important notice regarding Invoice **{{ $invoice->number }}** from **{{ $user->name }}**, which is now **overdue**.

@if($daysContext['days'] === 1)
Payment was due **1 day ago**.
@else
Payment was due **{{ $daysContext['days'] }} days ago**.
@endif

Please arrange payment immediately to bring your account current.
@endif

## Invoice Details

- **Invoice Number:** {{ $invoice->number }}
- **Issue Date:** {{ $invoice->issue_date->format('M d, Y') }}
- **Due Date:** {{ $invoice->due_date->format('M d, Y') }}
- **Total Amount:** {{ App\Helpers\CurrencyHelper::format($invoice->total, $user->currency) }}

<x-mail::button :url="config('app.url') . '/i/' . $invoice->public_token">
@if($reminder->type === 'after_due')
Pay Overdue Invoice Now
@else
View Invoice & Pay
@endif
</x-mail::button>

@if($reminder->type === 'before_due')
## Payment Instructions

Please view the full invoice online to see itemized details and payment options. We appreciate your prompt attention to this matter.
@endif

@if($reminder->type === 'on_due')
## Payment Instructions

Please view the full invoice online to see itemized details and payment options. Payment is due today, {{ $invoice->due_date->format('F d, Y') }}.
@endif

@if($reminder->type === 'after_due')
## Immediate Action Required

This invoice is now overdue. Please view the full invoice online and submit payment immediately. If you have already paid, please disregard this notice and accept our thanks.

If you have any questions or need assistance, please contact us right away.
@endif

Thank you,<br>
{{ $user->name }}
</x-mail::message>

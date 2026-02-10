<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\ReminderSchedule;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    private bool $includePdf = false;

    private ReminderSchedule $reminder;

    public Invoice $invoice;

    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(ReminderSchedule $reminder)
    {
        $this->reminder = $reminder;

        // Load relationships
        $this->reminder->load('invoice.client', 'invoice.items', 'invoice.user');
        $this->invoice = $this->reminder->invoice;
        $this->user = $this->invoice->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
            to: [$this->invoice->client->email],
            replyTo: [$this->user->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invoice-reminder',
            with: [
                'reminder' => $this->reminder,
                'daysContext' => $this->getDaysContext(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
   public function attachments(): array
{
    if (! $this->includePdf) {
        return [];
    }

    return [
        Attachment::fromData(
            fn () => Pdf::loadView('pdf.invoice', [
                'invoice' => $this->invoice,
                'user' => $this->user,
            ])->setPaper('A4', 'portrait')->output(),
            "invoice-{$this->invoice->number}.pdf"
        )->withMime('application/pdf'),
    ];
}



    /**
     * Enable PDF attachment for this email.
     */
    public function withPdf(): self
    {
        $this->includePdf = true;

        return $this;
    }

    /**
     * Get the subject line based on reminder type.
     */
    private function getSubject(): string
    {
        return match ($this->reminder->type) {
            'before_due' => "Upcoming payment due for Invoice {$this->invoice->number}",
            'on_due' => "Payment due today for Invoice {$this->invoice->number}",
            'after_due' => "Overdue payment reminder for Invoice {$this->invoice->number}",
            default => "Payment reminder for Invoice {$this->invoice->number}",
        };
    }

    /**
     * Get the days context for the template.
     *
     * @return array{days: int, is_past: bool, is_today: bool}
     */
    public function getDaysContext(): array
    {
        $now = now()->startOfDay();
        $dueDate = $this->invoice->due_date->startOfDay();
        $daysUntilDue = (int) $now->diffInDays($dueDate, false);

        return [
            'days' => abs($daysUntilDue),
            'is_past' => $daysUntilDue < 0,
            'is_today' => abs($daysUntilDue) === 0,
        ];
    }
}

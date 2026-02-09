<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSentMail extends Mailable
{
    use Queueable, SerializesModels;

    private bool $includePdf = false;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice,
        public User $user
    ) {
        // Load relationships if not already loaded
        $this->invoice->load('client', 'items');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice {$this->invoice->number} from {$this->user->name}",
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
            markdown: 'mail.invoice-sent',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->includePdf) {
            return [];
        }

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice,
            'user' => $this->user,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return [
            Attachment::fromData(fn () => $pdf->output(), "invoice-{$this->invoice->number}.pdf")
                ->withMime('application/pdf'),
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
}

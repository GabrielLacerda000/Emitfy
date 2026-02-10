<?php

use App\Mail\InvoiceReminderMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\ReminderSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'currency' => 'BRL',
    ]);

    $this->client = Client::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Client',
        'email' => 'client@example.com',
    ]);

    $this->invoice = Invoice::factory()->create([
        'user_id' => $this->user->id,
        'client_id' => $this->client->id,
        'number' => 'INV-001',
        'total' => 1500.00,
        'issue_date' => now()->subDays(5),
        'due_date' => now()->addDays(3),
    ]);
});

it('sends reminder email with correct subject for before_due type', function () {
    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'before_due',
        'offset_days' => 3,
    ]);

    Mail::fake();

    Mail::to($this->client->email)->send(new InvoiceReminderMail($reminder));

    Mail::assertSent(InvoiceReminderMail::class, function ($mail) {
        return $mail->hasTo($this->client->email)
            && $mail->envelope()->subject === "Upcoming payment due for Invoice {$this->invoice->number}";
    });
});

it('sends reminder email with correct subject for on_due type', function () {
    $this->invoice->update(['due_date' => now()]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'on_due',
        'offset_days' => 0,
    ]);

    Mail::fake();

    Mail::to($this->client->email)->send(new InvoiceReminderMail($reminder));

    Mail::assertSent(InvoiceReminderMail::class, function ($mail) {
        return $mail->hasTo($this->client->email)
            && $mail->envelope()->subject === "Payment due today for Invoice {$this->invoice->number}";
    });
});

it('sends reminder email with correct subject for after_due type', function () {
    $this->invoice->update(['due_date' => now()->subDays(7)]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'after_due',
        'offset_days' => 7,
    ]);

    Mail::fake();

    Mail::to($this->client->email)->send(new InvoiceReminderMail($reminder));

    Mail::assertSent(InvoiceReminderMail::class, function ($mail) {
        return $mail->hasTo($this->client->email)
            && $mail->envelope()->subject === "Overdue payment reminder for Invoice {$this->invoice->number}";
    });
});

it('sets correct recipient and reply-to addresses', function () {
    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'before_due',
    ]);

    Mail::fake();

    Mail::to($this->client->email)->send(new InvoiceReminderMail($reminder));

    Mail::assertSent(InvoiceReminderMail::class, function ($mail) {
        $envelope = $mail->envelope();

        return $mail->hasTo($this->client->email)
            && collect($envelope->replyTo)->contains(fn ($replyTo) => $replyTo->address === $this->user->email);
    });
});

it('calculates days context correctly for future due date', function () {
    $this->invoice->update(['due_date' => now()->addDays(5)]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'before_due',
    ]);

    $mail = new InvoiceReminderMail($reminder);
    $context = $mail->getDaysContext();

    expect($context['days'])->toBe(5)
        ->and($context['is_past'])->toBeFalse()
        ->and($context['is_today'])->toBeFalse();
});

it('calculates days context correctly for past due date', function () {
    $this->invoice->update(['due_date' => now()->subDays(3)]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'after_due',
    ]);

    $mail = new InvoiceReminderMail($reminder);
    $context = $mail->getDaysContext();

    expect($context['days'])->toBe(3)
        ->and($context['is_past'])->toBeTrue()
        ->and($context['is_today'])->toBeFalse();
});

it('calculates days context correctly for due today', function () {
    $this->invoice->update(['due_date' => now()]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'on_due',
    ]);

    $mail = new InvoiceReminderMail($reminder);
    $context = $mail->getDaysContext();

    expect($context['days'])->toBe(0)
        ->and($context['is_today'])->toBeTrue();
});

it('includes PDF attachment when withPdf is called', function () {
    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'before_due',
    ]);

    $mail = (new InvoiceReminderMail($reminder))->withPdf();

    $attachments = $mail->attachments();

    expect($attachments)->toHaveCount(1)
        ->and($attachments[0])->toBeInstanceOf(\Illuminate\Mail\Mailables\Attachment::class);
});

it('does not include PDF attachment by default', function () {
    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'before_due',
    ]);

    $mail = new InvoiceReminderMail($reminder);

    $attachments = $mail->attachments();

    expect($attachments)->toHaveCount(0);
});

it('loads all required relationships', function () {
    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $this->invoice->id,
        'type' => 'before_due',
    ]);

    $mail = new InvoiceReminderMail($reminder);

    expect($mail->invoice)->toBeInstanceOf(Invoice::class)
        ->and($mail->invoice->client)->toBeInstanceOf(Client::class)
        ->and($mail->invoice->user)->toBeInstanceOf(User::class)
        ->and($mail->user)->toBeInstanceOf(User::class);
});

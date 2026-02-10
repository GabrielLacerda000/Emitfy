<?php

use App\Enums\InvoiceStatus;
use App\Jobs\SendInvoiceReminderJob;
use App\Mail\InvoiceReminderMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ReminderSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

test('sends email with pdf to client', function () {
    Mail::fake();

    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    // Create invoice items separately
    InvoiceItem::factory()->count(2)->create([
        'invoice_id' => $invoice->id,
    ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $job = new SendInvoiceReminderJob($reminder);
    $job->handle();

    Mail::assertSent(InvoiceReminderMail::class, function ($mail) use ($client) {
        return $mail->hasTo($client->email);
    });
});

test('updates sent_at timestamp', function () {
    Mail::fake();

    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    // Create invoice items separately
    InvoiceItem::factory()->count(2)->create([
        'invoice_id' => $invoice->id,
    ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    expect($reminder->sent_at)->toBeNull();

    $job = new SendInvoiceReminderJob($reminder);
    $job->handle();

    $reminder->refresh();
    expect($reminder->sent_at)->not->toBeNull();
});

test('skips already sent reminders', function () {
    Mail::fake();

    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => now()->subDay(),
    ]);

    $job = new SendInvoiceReminderJob($reminder);
    $job->handle();

    Mail::assertNotSent(InvoiceReminderMail::class);
});

test('skips reminders for paid invoices', function () {
    Mail::fake();

    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::PAID,
            'due_date' => today()->addDays(3),
        ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $job = new SendInvoiceReminderJob($reminder);
    $job->handle();

    Mail::assertNotSent(InvoiceReminderMail::class);

    $reminder->refresh();
    expect($reminder->sent_at)->toBeNull();
});

test('logs failure information', function () {
    Log::spy();

    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $job = new SendInvoiceReminderJob($reminder);
    $exception = new \Exception('Mail server down');

    $job->failed($exception);

    Log::shouldHaveReceived('error')
        ->once()
        ->with('Failed to send invoice reminder email', \Mockery::on(function ($context) use ($reminder) {
            return $context['reminder_id'] === $reminder->id
                && $context['error'] === 'Mail server down';
        }));
});

test('has retry configuration', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $job = new SendInvoiceReminderJob($reminder);

    expect($job->tries)->toBe(3);
    expect($job->backoff)->toBe(60);
});

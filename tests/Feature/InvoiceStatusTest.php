<?php

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\ReminderSchedule;
use App\Models\User;

test('invoice can be marked as sent', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->draft()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);

    expect($invoice->isDraft())->toBeTrue();
    expect($invoice->sent_at)->toBeNull();

    $result = $invoice->markAsSent();

    expect($result)->toBeTrue();
    expect($invoice->fresh()->isSent())->toBeTrue();
    expect($invoice->fresh()->sent_at)->not->toBeNull();
});

test('invoice can be marked as paid', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);

    expect($invoice->isSent())->toBeTrue();
    expect($invoice->paid_at)->toBeNull();

    $result = $invoice->markAsPaid();

    expect($result)->toBeTrue();
    expect($invoice->fresh()->isPaid())->toBeTrue();
    expect($invoice->fresh()->paid_at)->not->toBeNull();
});

test('invoice can be marked as overdue', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);

    expect($invoice->isSent())->toBeTrue();

    $result = $invoice->markAsOverdue();

    expect($result)->toBeTrue();
    expect($invoice->fresh()->isOverdue())->toBeTrue();
});

test('paid invoice cannot transition to sent status', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->paid()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);

    expect($invoice->isPaid())->toBeTrue();

    $result = $invoice->markAsSent();

    expect($result)->toBeFalse();
    expect($invoice->fresh()->isPaid())->toBeTrue();
});

test('paid invoice cannot transition to overdue status', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->paid()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);

    expect($invoice->isPaid())->toBeTrue();

    $result = $invoice->markAsOverdue();

    expect($result)->toBeFalse();
    expect($invoice->fresh()->isPaid())->toBeTrue();
});

test('status check helpers work correctly', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $draftInvoice = Invoice::factory()->draft()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);
    expect($draftInvoice->isDraft())->toBeTrue();
    expect($draftInvoice->isSent())->toBeFalse();
    expect($draftInvoice->isPaid())->toBeFalse();
    expect($draftInvoice->isOverdue())->toBeFalse();

    $sentInvoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);
    expect($sentInvoice->isDraft())->toBeFalse();
    expect($sentInvoice->isSent())->toBeTrue();
    expect($sentInvoice->isPaid())->toBeFalse();
    expect($sentInvoice->isOverdue())->toBeFalse();

    $paidInvoice = Invoice::factory()->paid()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);
    expect($paidInvoice->isDraft())->toBeFalse();
    expect($paidInvoice->isSent())->toBeFalse();
    expect($paidInvoice->isPaid())->toBeTrue();
    expect($paidInvoice->isOverdue())->toBeFalse();

    $overdueInvoice = Invoice::factory()->overdue()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
    ]);
    expect($overdueInvoice->isDraft())->toBeFalse();
    expect($overdueInvoice->isSent())->toBeFalse();
    expect($overdueInvoice->isPaid())->toBeFalse();
    expect($overdueInvoice->isOverdue())->toBeTrue();
});

test('isPastDue correctly identifies overdue invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    // Sent invoice with past due date
    $pastDueInvoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDay(),
    ]);
    expect($pastDueInvoice->isPastDue())->toBeTrue();

    // Sent invoice with future due date
    $notDueInvoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->addDay(),
    ]);
    expect($notDueInvoice->isPastDue())->toBeFalse();

    // Draft invoice with past due date (not counted as past due)
    $draftInvoice = Invoice::factory()->draft()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDay(),
    ]);
    expect($draftInvoice->isPastDue())->toBeFalse();

    // Paid invoice with past due date (not counted as past due)
    $paidInvoice = Invoice::factory()->paid()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDay(),
    ]);
    expect($paidInvoice->isPastDue())->toBeFalse();
});

test('marking invoice as paid deletes pending reminder schedules', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
    ]);

    // Create unsent reminders
    ReminderSchedule::factory()->count(3)->create([
        'invoice_id' => $invoice->id,
        'sent_at' => null,
    ]);

    expect($invoice->reminderSchedules)->toHaveCount(3);

    $invoice->markAsPaid();

    expect($invoice->fresh()->reminderSchedules)->toHaveCount(0);
    expect($invoice->isPaid())->toBeTrue();
});

test('marking invoice as paid preserves sent reminder schedules', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
    ]);

    // Create mix of sent and unsent reminders
    $sentReminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'sent_at' => now()->subDays(2),
    ]);
    ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'on_due',
        'sent_at' => null,
    ]);
    ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'after_due',
        'sent_at' => null,
    ]);

    expect($invoice->reminderSchedules)->toHaveCount(3);

    $invoice->markAsPaid();

    $invoice->refresh();
    expect($invoice->reminderSchedules)->toHaveCount(1);
    expect($invoice->reminderSchedules->first()->id)->toBe($sentReminder->id);
});

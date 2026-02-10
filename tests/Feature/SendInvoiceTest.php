<?php

use App\Enums\InvoiceStatus;
use App\Jobs\SendInvoiceEmailJob;
use App\Mail\InvoiceSentMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\ReminderSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

test('draft invoice can be sent', function () {
    Queue::fake();
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
        'sent_at' => null,
    ]);

    $response = $this->actingAs($user)->post(route('invoices.send', $invoice));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Invoice sent successfully.');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::SENT);
    expect($invoice->sent_at)->not->toBeNull();

    Queue::assertPushed(SendInvoiceEmailJob::class, function ($job) use ($invoice) {
        return $job->invoice->id === $invoice->id;
    });
});

test('sent invoice can be resent', function () {
    Queue::fake();
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $originalSentAt = now()->subDays(5);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
        'sent_at' => $originalSentAt,
    ]);

    $response = $this->actingAs($user)->post(route('invoices.send', $invoice));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Invoice resent successfully.');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::SENT);
    expect($invoice->sent_at->isAfter($originalSentAt))->toBeTrue();

    Queue::assertPushed(SendInvoiceEmailJob::class, function ($job) use ($invoice) {
        return $job->invoice->id === $invoice->id;
    });
});

test('paid invoice cannot be sent', function () {
    Queue::fake();
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::PAID,
    ]);

    $response = $this->actingAs($user)->post(route('invoices.send', $invoice));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    Queue::assertNotPushed(SendInvoiceEmailJob::class);
});

test('users cannot send other users invoices', function () {
    Queue::fake();
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $otherUser->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    $response = $this->actingAs($user)->post(route('invoices.send', $invoice));

    $response->assertForbidden();

    Queue::assertNotPushed(SendInvoiceEmailJob::class);
});

test('job sends email with pdf attachment to client', function () {
    Mail::fake();
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
    ]);

    // Create and execute the job
    $job = new SendInvoiceEmailJob($invoice);
    $job->handle();

    // Assert email was sent to client
    Mail::assertSent(InvoiceSentMail::class, function ($mail) use ($client, $invoice) {
        return $mail->hasTo($client->email)
            && $mail->invoice->id === $invoice->id;
    });
});

test('sending draft invoice creates 3 reminder schedules', function () {
    Queue::fake();
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
        'due_date' => now()->addDays(10),
    ]);

    expect($invoice->reminderSchedules)->toHaveCount(0);

    $this->actingAs($user)->post(route('invoices.send', $invoice));

    $invoice->refresh();
    expect($invoice->reminderSchedules)->toHaveCount(3);

    $reminders = $invoice->reminderSchedules;
    expect($reminders->where('type', 'before_due')->first()->offset_days)->toBe(-3);
    expect($reminders->where('type', 'on_due')->first()->offset_days)->toBe(0);
    expect($reminders->where('type', 'after_due')->first()->offset_days)->toBe(7);
});

test('resending invoice does not create duplicate reminder schedules', function () {
    Queue::fake();
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
        'sent_at' => now()->subDays(5),
    ]);

    // Manually create reminders (simulating previous send)
    ReminderSchedule::factory()->count(3)->create([
        'invoice_id' => $invoice->id,
    ]);

    expect($invoice->fresh()->reminderSchedules)->toHaveCount(3);

    // Resend
    $this->actingAs($user)->post(route('invoices.send', $invoice));

    expect($invoice->fresh()->reminderSchedules)->toHaveCount(3); // Still 3, not 6
});

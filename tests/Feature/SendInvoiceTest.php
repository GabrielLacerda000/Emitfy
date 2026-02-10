<?php

use App\Enums\InvoiceStatus;
use App\Jobs\SendInvoiceEmailJob;
use App\Mail\InvoiceSentMail;
use App\Models\Client;
use App\Models\Invoice;
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
        return $job->invoiceId === $invoice->id;
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
        return $job->invoiceId === $invoice->id;
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
    $job = new SendInvoiceEmailJob($invoice->id);
    $job->handle();

    // Assert email was sent to client
    Mail::assertSent(InvoiceSentMail::class, function ($mail) use ($client, $invoice) {
        return $mail->hasTo($client->email)
            && $mail->invoice->id === $invoice->id;
    });
});

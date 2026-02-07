<?php

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

test('command marks overdue sent invoices as overdue', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    // Create sent invoice with past due date
    $overdueInvoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDays(5),
    ]);

    // Create sent invoice with future due date (should not be marked)
    $futureInvoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->addDays(5),
    ]);

    $this->artisan('invoices:mark-overdue')
        ->expectsOutput('Marked 1 invoice(s) as overdue.')
        ->assertExitCode(0);

    expect($overdueInvoice->fresh()->status)->toBe(InvoiceStatus::OVERDUE);
    expect($futureInvoice->fresh()->status)->toBe(InvoiceStatus::SENT);
});

test('command does not change paid invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    // Create paid invoice with past due date
    $paidInvoice = Invoice::factory()->paid()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDays(10),
    ]);

    $this->artisan('invoices:mark-overdue')
        ->expectsOutput('Marked 0 invoice(s) as overdue.')
        ->assertExitCode(0);

    expect($paidInvoice->fresh()->status)->toBe(InvoiceStatus::PAID);
});

test('command handles empty result set', function () {
    $this->artisan('invoices:mark-overdue')
        ->expectsOutput('Marked 0 invoice(s) as overdue.')
        ->assertExitCode(0);
});

test('command does not affect future invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    // Create sent invoice with future due date
    $futureInvoice = Invoice::factory()->sent()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->addWeek(),
    ]);

    $this->artisan('invoices:mark-overdue')
        ->expectsOutput('Marked 0 invoice(s) as overdue.')
        ->assertExitCode(0);

    expect($futureInvoice->fresh()->status)->toBe(InvoiceStatus::SENT);
});

test('command marks multiple overdue invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    // Create 3 overdue sent invoices
    Invoice::factory()->sent()->count(3)->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDays(3),
    ]);

    $this->artisan('invoices:mark-overdue')
        ->expectsOutput('Marked 3 invoice(s) as overdue.')
        ->assertExitCode(0);

    expect(Invoice::where('status', InvoiceStatus::OVERDUE)->count())->toBe(3);
});

test('command does not affect draft invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    // Create draft invoice with past due date
    $draftInvoice = Invoice::factory()->draft()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'due_date' => now()->subDays(10),
    ]);

    $this->artisan('invoices:mark-overdue')
        ->expectsOutput('Marked 0 invoice(s) as overdue.')
        ->assertExitCode(0);

    expect($draftInvoice->fresh()->status)->toBe(InvoiceStatus::DRAFT);
});

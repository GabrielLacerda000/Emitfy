<?php

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

test('invoices page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('invoices.index'));

    $response->assertOk();
});

test('create invoice page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('invoices.create'));

    $response->assertOk();
});

test('invoices can be created', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('invoices.store'), [
            'client_id' => $client->id,
            'issue_date' => '2026-02-01',
            'due_date' => '2026-02-28',
            'tax' => 10.00,
            'notes' => 'Test invoice notes',
            'status' => InvoiceStatus::DRAFT->value,
            'items' => [
                [
                    'description' => 'Web Development',
                    'quantity' => 10,
                    'unit_price' => 100.00,
                ],
                [
                    'description' => 'Consulting',
                    'quantity' => 5,
                    'unit_price' => 150.00,
                ],
            ],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('invoices.index'));

    $this->assertDatabaseHas('invoices', [
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT->value,
        'subtotal' => 1750.00,
        'tax' => 10.00,
        'total' => 1760.00,
    ]);

    $invoice = Invoice::where('user_id', $user->id)->first();
    expect($invoice->number)->toStartWith('INV-202602-');
    expect($invoice->public_token)->toHaveLength(32);
    expect($invoice->items)->toHaveCount(2);
});

test('invoice number is auto-generated with correct format', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'issue_date' => '2026-02-05',
        'due_date' => '2026-02-28',
        'tax' => 0,
        'status' => 'draft',
        'items' => [
            ['description' => 'Test', 'quantity' => 1, 'unit_price' => 100],
        ],
    ]);

    $invoice = Invoice::first();
    expect($invoice->number)->toMatch('/^INV-202602-\d{3}$/');
});

test('invoice numbers increment correctly for same user and month', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $invoiceData = [
        'client_id' => $client->id,
        'issue_date' => '2026-02-05',
        'due_date' => '2026-02-28',
        'tax' => 0,
        'status' => 'draft',
        'items' => [
            ['description' => 'Test', 'quantity' => 1, 'unit_price' => 100],
        ],
    ];

    $this->actingAs($user)->post(route('invoices.store'), $invoiceData);
    $this->actingAs($user)->post(route('invoices.store'), $invoiceData);
    $this->actingAs($user)->post(route('invoices.store'), $invoiceData);

    $invoices = Invoice::orderBy('number')->get();
    expect($invoices[0]->number)->toBe('INV-202602-001');
    expect($invoices[1]->number)->toBe('INV-202602-002');
    expect($invoices[2]->number)->toBe('INV-202602-003');
});

test('invoice totals are calculated server-side', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->post(route('invoices.store'), [
        'client_id' => $client->id,
        'issue_date' => '2026-02-01',
        'due_date' => '2026-02-28',
        'tax' => 25.50,
        'status' => 'draft',
        'items' => [
            ['description' => 'Item 1', 'quantity' => 3, 'unit_price' => 50.00],
            ['description' => 'Item 2', 'quantity' => 2, 'unit_price' => 75.25],
        ],
    ]);

    $invoice = Invoice::first();
    expect($invoice->subtotal)->toBe('300.50');
    expect($invoice->tax)->toBe('25.50');
    expect($invoice->total)->toBe('326.00');
});

test('show invoice page can be rendered', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('invoices.show', $invoice));

    $response->assertOk();
});

test('edit invoice page can be rendered', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('invoices.edit', $invoice));

    $response->assertOk();
});

test('invoices can be updated', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->put(route('invoices.update', $invoice), [
            'client_id' => $client->id,
            'issue_date' => '2026-02-10',
            'due_date' => '2026-03-10',
            'tax' => 15.00,
            'notes' => 'Updated notes',
            'status' => InvoiceStatus::SENT->value,
            'items' => [
                ['description' => 'Updated Service', 'quantity' => 5, 'unit_price' => 200.00],
            ],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('invoices.index'));

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::SENT);
    expect($invoice->subtotal)->toBe('1000.00');
    expect($invoice->total)->toBe('1015.00');
});

test('paid invoices can be updated with paid_at set to today', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
    ]);

    $today = now()->toDateString();

    $response = $this
        ->actingAs($user)
        ->put(route('invoices.update', $invoice), [
            'client_id' => $client->id,
            'issue_date' => '2026-02-10',
            'due_date' => '2026-03-10',
            'tax' => 15.00,
            'notes' => 'Updated notes',
            'status' => InvoiceStatus::PAID->value,
            'paid_at' => $today,
            'items' => [
                ['description' => 'Updated Service', 'quantity' => 5, 'unit_price' => 200.00],
            ],
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('invoices.index'));

    $invoice->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::PAID);
    expect($invoice->paid_at)->not->toBeNull();
    expect($invoice->paid_at?->toDateString())->toBe($today);
});

test('invoices can be deleted', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);
    $invoice = Invoice::factory()->create(['user_id' => $user->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('invoices.destroy', $invoice));

    $response->assertRedirect(route('invoices.index'));

    $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
});

test('users cannot view other users invoices', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);
    $invoice = Invoice::factory()->create(['user_id' => $otherUser->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('invoices.show', $invoice));

    $response->assertForbidden();
});

test('users cannot edit other users invoices', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);
    $invoice = Invoice::factory()->create(['user_id' => $otherUser->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('invoices.edit', $invoice));

    $response->assertForbidden();
});

test('users cannot update other users invoices', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);
    $invoice = Invoice::factory()->create(['user_id' => $otherUser->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->put(route('invoices.update', $invoice), [
            'client_id' => $client->id,
            'issue_date' => '2026-02-01',
            'due_date' => '2026-02-28',
            'tax' => 0,
            'status' => InvoiceStatus::DRAFT->value,
            'items' => [
                ['description' => 'Hacked', 'quantity' => 1, 'unit_price' => 1],
            ],
        ]);

    $response->assertForbidden();
});

test('users cannot delete other users invoices', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);
    $invoice = Invoice::factory()->create(['user_id' => $otherUser->id, 'client_id' => $client->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('invoices.destroy', $invoice));

    $response->assertForbidden();

    $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
});

test('validation errors are returned for invalid invoice data', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('invoices.store'), [
            'client_id' => 999999,
            'issue_date' => 'not-a-date',
            'due_date' => '2026-01-01',
            'tax' => -10,
            'status' => 'invalid-status',
            'items' => [],
        ]);

    $response->assertSessionHasErrors(['client_id', 'issue_date', 'tax', 'status', 'items']);
});

test('due date must be after or equal to issue date', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('invoices.store'), [
            'client_id' => $client->id,
            'issue_date' => '2026-02-28',
            'due_date' => '2026-02-01',
            'tax' => 0,
            'status' => InvoiceStatus::DRAFT->value,
            'items' => [
                ['description' => 'Test', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

    $response->assertSessionHasErrors(['due_date']);
});

test('invoice must have at least one item', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('invoices.store'), [
            'client_id' => $client->id,
            'issue_date' => '2026-02-01',
            'due_date' => '2026-02-28',
            'tax' => 0,
            'status' => InvoiceStatus::DRAFT->value,
            'items' => [],
        ]);

    $response->assertSessionHasErrors(['items']);
});

test('users cannot create invoices for other users clients', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('invoices.store'), [
            'client_id' => $otherClient->id,
            'issue_date' => '2026-02-01',
            'due_date' => '2026-02-28',
            'tax' => 0,
            'status' => InvoiceStatus::DRAFT->value,
            'items' => [
                ['description' => 'Test', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

    $response->assertSessionHasErrors(['client_id']);
});

test('guests cannot access invoices', function () {
    $response = $this->get(route('invoices.index'));

    $response->assertRedirect(route('login'));
});

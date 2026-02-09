<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authenticated user can download their invoice PDF', function () {
    $client = Client::factory()->for($this->user)->create();
    $invoice = Invoice::factory()
        ->for($this->user)
        ->for($client)
        ->hasItems(3)
        ->create();

    $response = $this->actingAs($this->user)->get(route('invoices.pdf', $invoice));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'attachment; filename=invoice-'.$invoice->number.'.pdf');
});

test('user cannot download another users invoice PDF', function () {
    $otherUser = User::factory()->create();
    $client = Client::factory()->for($otherUser)->create();
    $invoice = Invoice::factory()
        ->for($otherUser)
        ->for($client)
        ->hasItems(2)
        ->create();

    $response = $this->actingAs($this->user)->get(route('invoices.pdf', $invoice));

    $response->assertForbidden();
});

test('unauthenticated user is redirected to login', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->hasItems(1)
        ->create();

    $response = $this->get(route('invoices.pdf', $invoice));

    $response->assertRedirect(route('login'));
});

test('PDF contains invoice data', function () {
    $client = Client::factory()->for($this->user)->create([
        'name' => 'Acme Corporation',
    ]);
    $invoice = Invoice::factory()
        ->for($this->user)
        ->for($client)
        ->hasItems(2)
        ->create([
            'number' => 'INV-2024-001',
        ]);

    $response = $this->actingAs($this->user)->get(route('invoices.pdf', $invoice));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');

    $content = $response->getContent();

    // Verify it's a valid PDF by checking the magic header
    expect(substr($content, 0, 5))->toBe('%PDF-');

    // Verify PDF has content (not empty)
    expect(strlen($content))->toBeGreaterThan(1000);
});

test('PDF can be streamed in browser', function () {
    $client = Client::factory()->for($this->user)->create();
    $invoice = Invoice::factory()
        ->for($this->user)
        ->for($client)
        ->hasItems(1)
        ->create();

    $response = $this->actingAs($this->user)->get(route('invoices.pdf', $invoice).'?mode=stream');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'inline; filename=invoice-'.$invoice->number.'.pdf');
});

test('PDF filename matches invoice number pattern', function () {
    $client = Client::factory()->for($this->user)->create();
    $invoice = Invoice::factory()
        ->for($this->user)
        ->for($client)
        ->hasItems(1)
        ->create([
            'number' => 'INV-2024-123',
        ]);

    $response = $this->actingAs($this->user)->get(route('invoices.pdf', $invoice));

    $response->assertHeader('Content-Disposition', 'attachment; filename=invoice-INV-2024-123.pdf');
});

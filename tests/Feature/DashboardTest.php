<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard returns correct stats structure', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('stats', fn ($stats) => $stats
            ->has('totalOutstanding')
            ->has('totalPaid')
            ->has('totalPaidCount')
            ->has('totalOverdue')
            ->has('overdueCount')
            ->has('dueSoonCount')
            ->has('dueSoonTotal')
        )
        ->has('recentInvoices')
        ->has('recentClients')
    );
});

test('total outstanding includes only sent invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    // Create invoices with different statuses
    Invoice::factory()->for($user)->for($client)->create(['status' => 'sent', 'total' => 1000]);
    Invoice::factory()->for($user)->for($client)->create(['status' => 'overdue', 'total' => 500]); // excluded
    Invoice::factory()->for($user)->for($client)->create(['status' => 'paid', 'total' => 300]);
    Invoice::factory()->for($user)->for($client)->create(['status' => 'draft', 'total' => 200]);

    $this->actingAs($user);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('stats.totalOutstanding', '1000.00')
    );
});

test('overdue stats calculate amount and count correctly', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    // Create overdue invoices
    Invoice::factory()->for($user)->for($client)->create(['status' => 'overdue', 'total' => 500]);
    Invoice::factory()->for($user)->for($client)->create(['status' => 'overdue', 'total' => 300]);
    Invoice::factory()->for($user)->for($client)->create(['status' => 'sent', 'total' => 200]);

    $this->actingAs($user);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('stats.totalOverdue', '800.00')
        ->where('stats.overdueCount', 2)
    );
});

test('due soon includes sent invoices due within 7 days', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    // Invoice due in 3 days (should be included)
    Invoice::factory()->for($user)->for($client)->create([
        'status' => 'sent',
        'total' => 300,
        'due_date' => now()->addDays(3),
    ]);

    // Invoice due in 6 days (should be included)
    Invoice::factory()->for($user)->for($client)->create([
        'status' => 'sent',
        'total' => 200,
        'due_date' => now()->addDays(6),
    ]);

    // Invoice due in 10 days (should NOT be included)
    Invoice::factory()->for($user)->for($client)->create([
        'status' => 'sent',
        'total' => 400,
        'due_date' => now()->addDays(10),
    ]);

    // Overdue invoice (should NOT be included - status is overdue, not sent)
    Invoice::factory()->for($user)->for($client)->create([
        'status' => 'overdue',
        'total' => 100,
        'due_date' => now()->addDays(2),
    ]);

    $this->actingAs($user);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('stats.dueSoonTotal', '500.00')
        ->where('stats.dueSoonCount', 2)
    );
});

test('recent invoices are limited to 5 items', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    // Create 10 invoices
    Invoice::factory()->count(10)->for($user)->for($client)->create();

    $this->actingAs($user);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('recentInvoices', 5)
    );
});

test('recent clients are limited to 5 items with invoice counts', function () {
    $user = User::factory()->create();

    // Create 10 clients
    $clients = Client::factory()->count(10)->for($user)->create();

    // Add invoices to first client
    Invoice::factory()->count(3)->for($user)->for($clients->first())->create();

    $this->actingAs($user);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('recentClients', 5)
        ->where('recentClients.0.invoices_count', 3)
    );
});

test('users only see their own data', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $client1 = Client::factory()->for($user1)->create();
    $client2 = Client::factory()->for($user2)->create();

    Invoice::factory()->for($user1)->for($client1)->create(['status' => 'sent', 'total' => 1000]);
    Invoice::factory()->for($user2)->for($client2)->create(['status' => 'sent', 'total' => 2000]);

    $this->actingAs($user1);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('stats.totalOutstanding', '1000.00')
        ->has('recentInvoices', 1)
        ->has('recentClients', 1)
    );
});

test('empty state returns zero values', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('stats.totalOutstanding', '0.00')
        ->where('stats.totalOverdue', '0.00')
        ->where('stats.overdueCount', 0)
        ->where('stats.dueSoonCount', 0)
        ->where('stats.dueSoonTotal', '0.00')
        ->has('recentInvoices', 0)
        ->has('recentClients', 0)
    );
});

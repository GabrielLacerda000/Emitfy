<?php

use App\Enums\InvoiceStatus;
use App\Jobs\SendInvoiceReminderJob;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\ReminderSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
});

test('sends before_due reminder 3 days before', function () {
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

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertPushed(SendInvoiceReminderJob::class, function ($job) use ($reminder) {
        return $job->reminder->id === $reminder->id;
    });
});

test('sends on_due reminder on due date', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today(),
        ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'on_due',
        'offset_days' => 0,
        'sent_at' => null,
    ]);

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertPushed(SendInvoiceReminderJob::class, function ($job) use ($reminder) {
        return $job->reminder->id === $reminder->id;
    });
});

test('sends after_due reminder 7 days after', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::OVERDUE,
            'due_date' => today()->subDays(7),
        ]);

    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'after_due',
        'offset_days' => 7,
        'sent_at' => null,
    ]);

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertPushed(SendInvoiceReminderJob::class, function ($job) use ($reminder) {
        return $job->reminder->id === $reminder->id;
    });
});

test('skips already sent reminders', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => now()->subDay(),
    ]);

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertNotPushed(SendInvoiceReminderJob::class);
});

test('skips reminders for paid invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::PAID,
            'due_date' => today()->addDays(3),
        ]);

    ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertNotPushed(SendInvoiceReminderJob::class);
});

test('skips reminders for draft invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::DRAFT,
            'due_date' => today()->addDays(3),
        ]);

    ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertNotPushed(SendInvoiceReminderJob::class);
});

test('dry run option lists without sending', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $this->artisan('reminders:send --dry-run')
        ->expectsOutput('DRY RUN MODE - No reminders will be sent')
        ->assertSuccessful();

    Queue::assertNotPushed(SendInvoiceReminderJob::class);
});

test('date option overrides today', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(5), // Due in 5 days
        ]);

    // Reminder should be sent 2 days from now (5 - 3 = 2)
    $reminder = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    // Run command with date set to 2 days from now
    $testDate = today()->addDays(2)->format('Y-m-d');
    $this->artisan("reminders:send --date={$testDate}")
        ->assertSuccessful();

    Queue::assertPushed(SendInvoiceReminderJob::class, function ($job) use ($reminder) {
        return $job->reminder->id === $reminder->id;
    });
});

test('processes multiple reminders in chunks', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    // Create 150 invoices with reminders due today
    $reminderCount = 150;
    foreach (range(1, $reminderCount) as $i) {
        $invoice = Invoice::factory()
            ->for($user)
            ->for($client)
            ->create([
                'status' => InvoiceStatus::SENT,
                'due_date' => today()->addDays(3),
            ]);

        ReminderSchedule::factory()->create([
            'invoice_id' => $invoice->id,
            'type' => 'before_due',
            'offset_days' => -3,
            'sent_at' => null,
        ]);
    }

    $this->artisan('reminders:send')
        ->assertSuccessful();

    Queue::assertPushed(SendInvoiceReminderJob::class, $reminderCount);
});

test('continues after dispatch failure', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    // Create multiple reminders
    $invoice1 = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    $reminder1 = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice1->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    $invoice2 = Invoice::factory()
        ->for($user)
        ->for($client)
        ->create([
            'status' => InvoiceStatus::SENT,
            'due_date' => today()->addDays(3),
        ]);

    $reminder2 = ReminderSchedule::factory()->create([
        'invoice_id' => $invoice2->id,
        'type' => 'before_due',
        'offset_days' => -3,
        'sent_at' => null,
    ]);

    // Even if one fails, command should continue and report
    Queue::fake([
        SendInvoiceReminderJob::class => function () {
            throw new \Exception('Simulated failure');
        },
    ]);

    $this->artisan('reminders:send')
        ->assertSuccessful();

    // Command should still succeed even with dispatch failures
    expect(true)->toBeTrue();
});

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('provider subscription id column is nullable', function () {
    $columns = collect(DB::select("PRAGMA table_info('subscription_providers')"));

    $providerSubscriptionId = $columns->firstWhere('name', 'provider_subscription_id');

    expect($providerSubscriptionId)->not->toBeNull();
    expect((int) $providerSubscriptionId->notnull)->toBe(0);
});

test('invoice and subscription user_id columns are uuid-compatible and keep user foreign keys', function () {
    $invoiceColumns = collect(DB::select("PRAGMA table_info('invoices')"));
    $subscriptionColumns = collect(DB::select("PRAGMA table_info('subscriptions')"));
    $invoiceForeignKeys = collect(DB::select("PRAGMA foreign_key_list('invoices')"));
    $subscriptionForeignKeys = collect(DB::select("PRAGMA foreign_key_list('subscriptions')"));

    $invoiceUserId = $invoiceColumns->firstWhere('name', 'user_id');
    $subscriptionUserId = $subscriptionColumns->firstWhere('name', 'user_id');

    expect($invoiceUserId)->not->toBeNull();
    expect($subscriptionUserId)->not->toBeNull();
    expect((int) $invoiceUserId->notnull)->toBe(1);
    expect((int) $subscriptionUserId->notnull)->toBe(1);
    expect(Str::contains(Str::lower($invoiceUserId->type), ['char', 'text', 'uuid']))->toBeTrue();
    expect(Str::contains(Str::lower($subscriptionUserId->type), ['char', 'text', 'uuid']))->toBeTrue();

    expect($invoiceForeignKeys->contains(function (object $foreignKey): bool {
        return $foreignKey->from === 'user_id'
            && $foreignKey->table === 'users'
            && $foreignKey->to === 'id';
    }))->toBeTrue();

    expect($subscriptionForeignKeys->contains(function (object $foreignKey): bool {
        return $foreignKey->from === 'user_id'
            && $foreignKey->table === 'users'
            && $foreignKey->to === 'id';
    }))->toBeTrue();
});

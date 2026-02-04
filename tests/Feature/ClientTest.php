<?php

use App\Models\Client;
use App\Models\User;

test('clients page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('clients.index'));

    $response->assertOk();
});

test('create client page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('clients.create'));

    $response->assertOk();
});

test('clients can be created', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('clients.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company_name' => 'Acme Inc',
            'notes' => 'Some notes about the client',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'user_id' => $user->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'company_name' => 'Acme Inc',
        'notes' => 'Some notes about the client',
    ]);
});

test('clients can be created with minimal data', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('clients.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'user_id' => $user->id,
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

test('edit client page can be rendered', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('clients.edit', $client));

    $response->assertOk();
});

test('clients can be updated', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->put(route('clients.update', $client), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'company_name' => 'Updated Company',
            'notes' => 'Updated notes',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('clients.index'));

    $client->refresh();

    expect($client->name)->toBe('Updated Name');
    expect($client->email)->toBe('updated@example.com');
    expect($client->company_name)->toBe('Updated Company');
    expect($client->notes)->toBe('Updated notes');
});

test('clients can be deleted', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('clients.destroy', $client));

    $response->assertRedirect(route('clients.index'));

    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});

test('users cannot access other users clients on edit', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('clients.edit', $client));

    $response->assertForbidden();
});

test('users cannot update other users clients', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->put(route('clients.update', $client), [
            'name' => 'Hacked Name',
            'email' => 'hacked@example.com',
        ]);

    $response->assertForbidden();

    $client->refresh();
    expect($client->name)->not->toBe('Hacked Name');
});

test('users cannot delete other users clients', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $otherUser->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('clients.destroy', $client));

    $response->assertForbidden();

    $this->assertDatabaseHas('clients', ['id' => $client->id]);
});

test('validation errors are returned for invalid data', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('clients.store'), [
            'name' => '',
            'email' => 'not-an-email',
        ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('client email must be unique per user', function () {
    $user = User::factory()->create();
    Client::factory()->create([
        'user_id' => $user->id,
        'email' => 'duplicate@example.com',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('clients.store'), [
            'name' => 'Another Client',
            'email' => 'duplicate@example.com',
        ]);

    $response->assertSessionHasErrors(['email']);
});

test('different users can have clients with the same email', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Client::factory()->create([
        'user_id' => $user1->id,
        'email' => 'shared@example.com',
    ]);

    $response = $this
        ->actingAs($user2)
        ->post(route('clients.store'), [
            'name' => 'Same Email Client',
            'email' => 'shared@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'user_id' => $user2->id,
        'email' => 'shared@example.com',
    ]);
});

test('client can keep same email on update', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'email' => 'existing@example.com',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('clients.update', $client), [
            'name' => 'Updated Name',
            'email' => 'existing@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('clients.index'));
});

test('guests cannot access clients', function () {
    $response = $this->get(route('clients.index'));

    $response->assertRedirect(route('login'));
});

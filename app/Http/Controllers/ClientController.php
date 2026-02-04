<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the user's clients.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('clients/Index', [
            'clients' => $request->user()->clients()->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): Response
    {
        return Inertia::render('clients/Create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $request->user()->clients()->create($request->validated());

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Request $request, Client $client): Response
    {
        if ($client->user_id !== $request->user()->id) {
            abort(403);
        }

        return Inertia::render('clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        if ($client->user_id !== $request->user()->id) {
            abort(403);
        }

        $client->update($request->validated());

        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Request $request, Client $client): RedirectResponse
    {
        if ($client->user_id !== $request->user()->id) {
            abort(403);
        }

        $client->delete();

        return redirect()->route('clients.index');
    }
}

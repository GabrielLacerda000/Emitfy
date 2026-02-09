<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\BusinessSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BusinessSettingsController extends Controller
{
    /**
     * Show the user's business settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Business', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's business settings.
     */
    public function update(BusinessSettingsRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return to_route('business.edit');
    }
}

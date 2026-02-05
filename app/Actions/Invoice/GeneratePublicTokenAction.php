<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Str;

class GeneratePublicTokenAction
{
    /**
     * Generate a unique public token for invoice sharing.
     */
    public function __invoke(): string
    {
        do {
            $token = Str::random(32);
        } while (Invoice::where('public_token', $token)->exists());

        return $token;
    }
}

<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPagueDevWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagueDevWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('X-Webhook-Signature');

        if (! $this->verifySignature($rawBody, $signature)) {
            Log::warning('PagueDevWebhook: invalid signature', [
                'ip'        => $request->ip(),
                'timestamp' => $request->header('X-Webhook-Timestamp'),
            ]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        ProcessPagueDevWebhook::dispatch(json_decode($rawBody, true) ?? []);

        return response()->json(['ok' => true]);
    }

    private function verifySignature(string $rawBody, ?string $signature): bool
    {
        if (! $signature) {
            return false;
        }

        $secret = config('services.pagar_dev.webhook_secret');

        if (! $secret) {
            return false;
        }

        // pague.dev spec: signing_key = SHA256(webhook_secret) as raw bytes
        $signingKey = hex2bin(hash('sha256', $secret));
        $expected   = hash_hmac('sha256', $rawBody, $signingKey);

        return hash_equals($expected, $signature);
    }
}

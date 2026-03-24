<?php

namespace App\Jobs;

use App\Dto\Webhooks\PagueDevWebhookPayload;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Gateways\PaguedevGateway;
use App\Models\SubscriptionPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPagueDevWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [2, 5, 10, 20, 30];

    public function __construct(public readonly PagueDevWebhookPayload $payload) {}

    public function handle(PaguedevGateway $gateway): void
    {
        $eventId = $this->payload->eventId;
        $event   = $this->payload->event;
        $data    = $this->payload->data;

        // Idempotency: skip if this eventId was already processed
        if ($eventId && SubscriptionPayment::whereJsonContains('raw_payload->eventId', $eventId)->exists()) {
            Log::info('ProcessPagueDevWebhook: duplicate event, skipping', ['eventId' => $eventId]);

            return;
        }

        // Find payment by external_payment_id; fallback to externalReference
        $payment = SubscriptionPayment::where('external_payment_id', $data['id'] ?? null)->first();

        if (! $payment && isset($data['externalReference']) && str_starts_with($data['externalReference'], 'sub:')) {
            $subId   = (int) substr($data['externalReference'], 4);
            $payment = SubscriptionPayment::where('subscription_id', $subId)->latest()->first();
        }

        if (! $payment) {
            Log::warning('ProcessPagueDevWebhook: payment not found', [
                'externalId'       => $data['id'] ?? null,
                'externalReference' => $data['externalReference'] ?? null,
            ]);

            return;
        }

        $newStatus = $gateway->mapStatus($event);

        $payment->update([
            'status'      => $newStatus,
            'paid_at'     => $newStatus === PaymentStatus::PAID ? now() : $payment->paid_at,
            'raw_payload' => array_merge($payment->raw_payload ?? [], [
                'eventId'        => $eventId,
                'webhookEvent'   => $event,
                'webhookData'    => $data,
            ]),
        ]);

        $subscription = $payment->subscription;
        $provider     = $subscription?->activeProvider;

        if ($provider) {
            $provider->update([
                'last_provider_payment_id' => $data['id'] ?? null,
                'status'                   => $newStatus->value,
            ]);
        }

        if ($newStatus === PaymentStatus::PAID && $subscription) {
            $subscription->update([
                'status'             => SubscriptionStatus::ACTIVE,
                'current_period_end' => $subscription->billing_cycle === 'yearly'
                    ? now()->addDays(365)
                    : now()->addDays(30),
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ProcessPagueDevWebhook failed', [
            'error'   => $e->getMessage(),
            'payload' => $this->payload,
        ]);
    }
}

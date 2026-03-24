<?php

namespace App\Dto\Webhooks;

readonly class PagueDevWebhookPayload
{
    public function __construct(
        public ?string $eventId,
        public ?string $event,
        public array   $data,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            eventId: $payload['eventId'] ?? null,
            event:   $payload['event']   ?? null,
            data:    $payload['data']    ?? [],
        );
    }
}

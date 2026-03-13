<?php

namespace App\Dto\Payments;

readonly class SubscriptionResponse
{
    public function __construct(
        public string $id,
        public string $customer,
        public string $status,
        public ?float $value = null,
        public ?string $nextDueDate = null,
        public ?string $cycle = null,
        public ?string $billingType = null,
        public bool $deleted = false,
        public ?string $dateCreated = null,
        public ?string $endDate = null,
        public ?string $description = null,
        public ?string $externalReference = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            customer: $data['customer'],
            status: $data['status'],
            value: isset($data['value']) ? (float) $data['value'] : null,
            nextDueDate: $data['nextDueDate'] ?? null,
            cycle: $data['cycle'] ?? null,
            billingType: $data['billingType'] ?? null,
            deleted: $data['deleted'] ?? false,
            dateCreated: $data['dateCreated'] ?? null,
            endDate: $data['endDate'] ?? null,
            description: $data['description'] ?? null,
            externalReference: $data['externalReference'] ?? null,
        );
    }
}

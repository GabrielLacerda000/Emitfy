<?php

namespace App\Dto\Asaas;

readonly class SubscriptionResponse
{
    public function __construct(
        public string $id,
        public string $customer,
        public string $status,
        public float $value,
        public string $nextDueDate,
        public string $cycle,
        public string $billingType,
        public bool $deleted,
        public string $dateCreated,
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
            value: (float) $data['value'],
            nextDueDate: $data['nextDueDate'],
            cycle: $data['cycle'],
            billingType: $data['billingType'],
            deleted: $data['deleted'] ?? false,
            dateCreated: $data['dateCreated'],
            endDate: $data['endDate'] ?? null,
            description: $data['description'] ?? null,
            externalReference: $data['externalReference'] ?? null,
        );
    }
}

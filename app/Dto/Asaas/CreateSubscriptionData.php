<?php

namespace App\Dto\Asaas;

use App\Enums\AsaasBillingType;

readonly class CreateSubscriptionData
{
    public function __construct(
        public string $customer,
        public AsaasBillingType $billingType,
        public float $value,
        public string $nextDueDate,
        public string $cycle,
        public ?string $description = null,
        public ?string $endDate = null,
        public ?int $maxPayments = null,
        public ?string $externalReference = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'customer' => $this->customer,
            'billingType' => $this->billingType->value,
            'value' => $this->value,
            'nextDueDate' => $this->nextDueDate,
            'cycle' => $this->cycle,
            'description' => $this->description,
            'endDate' => $this->endDate,
            'maxPayments' => $this->maxPayments,
            'externalReference' => $this->externalReference,
        ], fn ($value) => $value !== null);
    }
}

<?php

namespace App\Dto\Payments;

use App\Dto\Payments\CustomerData;

readonly class ChargeData
{
    public function __construct(
        public CustomerData $customer,
        public float $amount,
        public string $currency,
        public string $description,
        public ?string $dueDate = null,
        public ?string $paymentMethod = null,
        public array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'customer' => is_string($this->customer)
                ? $this->customer
                : $this->customer->toArray(),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'dueDate' => $this->dueDate,
            'paymentMethod' => $this->paymentMethod,
            'metadata' => $this->metadata,
        ];
    }
}
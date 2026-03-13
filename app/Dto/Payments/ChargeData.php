<?php

namespace App\Dto\Payments;

class ChargeData
{
    public function __construct(
        public string $customerId,
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
            'customer' => $this->customerId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'dueDate' => $this->dueDate,
            'paymentMethod' => $this->paymentMethod,
            'metadata' => $this->metadata,
        ];
    }
}
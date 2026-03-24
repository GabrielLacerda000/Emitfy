<?php

namespace App\Dto\Payments;

class CustomerData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $name,
        public ?string $email,
        public string $document,
        public ?string $phone,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'document' => $this->document,
            'phone' => $this->phone,
        ];
    }
}

<?php

namespace App\Dto\Payments;

readonly class CreditCardTokenResponse
{
    public function __construct(
        public string $creditCardToken,
        public string $creditCardNumber,
        public string $creditCardBrand,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            creditCardToken: $data['creditCardToken'],
            creditCardNumber: $data['creditCardNumber'],
            creditCardBrand: $data['creditCardBrand'],
        );
    }
}

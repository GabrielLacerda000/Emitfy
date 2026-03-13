<?php

namespace App\Dto\Payments;

readonly class TokenizeCreditCardData
{
    public function __construct(
        public string $customer,
        public string $remoteIp,
        public string $holderName,
        public string $number,
        public string $expiryMonth,
        public string $expiryYear,
        public string $ccv,
        public string $holderInfoName,
        public string $holderInfoEmail,
        public string $holderInfoCpfCnpj,
        public string $holderInfoPostalCode,
        public string $holderInfoAddressNumber,
        public string $holderInfoPhone,
        public ?string $holderInfoAddressComplement = null,
        public ?string $holderInfoMobilePhone = null,
    ) {}
}

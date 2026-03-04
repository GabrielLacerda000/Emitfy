<?php

namespace App\Dto\Asaas;

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

    public function toArray(): array
    {
        return [
            'customer' => $this->customer,
            'remoteIp' => $this->remoteIp,
            'creditCard' => [
                'holderName' => $this->holderName,
                'number' => $this->number,
                'expiryMonth' => $this->expiryMonth,
                'expiryYear' => $this->expiryYear,
                'ccv' => $this->ccv,
            ],
            'creditCardHolderInfo' => array_filter([
                'name' => $this->holderInfoName,
                'email' => $this->holderInfoEmail,
                'cpfCnpj' => $this->holderInfoCpfCnpj,
                'postalCode' => $this->holderInfoPostalCode,
                'addressNumber' => $this->holderInfoAddressNumber,
                'addressComplement' => $this->holderInfoAddressComplement,
                'phone' => $this->holderInfoPhone,
                'mobilePhone' => $this->holderInfoMobilePhone,
            ], fn ($v) => $v !== null),
        ];
    }
}

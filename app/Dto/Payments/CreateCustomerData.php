<?php

namespace App\Dto\Payments;

readonly class CreateCustomerData
{
    public function __construct(
        public string $name,
        public string $cpfCnpj,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $mobilePhone = null,
        public ?string $address = null,
        public ?string $addressNumber = null,
        public ?string $complement = null,
        public ?string $province = null,
        public ?string $postalCode = null,
        public ?string $externalReference = null,
        public ?bool $notificationDisabled = null,
        public ?string $additionalEmails = null,
        public ?string $observations = null,
        public ?string $groupName = null,
        public ?string $company = null,
        public ?bool $foreignCustomer = null,
    ) {}
}

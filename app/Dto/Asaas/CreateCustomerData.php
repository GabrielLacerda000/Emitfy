<?php

namespace App\Dto\Asaas;

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

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'cpfCnpj' => $this->cpfCnpj,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobilePhone' => $this->mobilePhone,
            'address' => $this->address,
            'addressNumber' => $this->addressNumber,
            'complement' => $this->complement,
            'province' => $this->province,
            'postalCode' => $this->postalCode,
            'externalReference' => $this->externalReference,
            'notificationDisabled' => $this->notificationDisabled,
            'additionalEmails' => $this->additionalEmails,
            'observations' => $this->observations,
            'groupName' => $this->groupName,
            'company' => $this->company,
            'foreignCustomer' => $this->foreignCustomer,
        ], fn ($value) => $value !== null);
    }
}

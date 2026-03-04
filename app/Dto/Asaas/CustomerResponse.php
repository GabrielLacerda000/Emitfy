<?php

namespace App\Dto\Asaas;

readonly class CustomerResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $cpfCnpj,
        public string $personType,
        public bool $deleted,
        public string $dateCreated,
        public ?string $email = null,
        public ?string $externalReference = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            cpfCnpj: $data['cpfCnpj'],
            personType: $data['personType'],
            deleted: $data['deleted'] ?? false,
            dateCreated: $data['dateCreated'],
            email: $data['email'] ?? null,
            externalReference: $data['externalReference'] ?? null,
        );
    }
}

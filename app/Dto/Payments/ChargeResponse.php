<?php

namespace App\Dto\Payments;

readonly class ChargeResponse
{
    public function __construct(
        public string $externalPaymentId,
        public string $status,
        public ?float $amount = null,
        public ?string $dueDate = null,
        public ?string $billingType = null,
        public ?string $invoiceUrl = null,
        public ?string $pixCode = null,
        public ?string $barCode = null,
        public ?string $expiresAt = null,
        public ?string $qrCodeBase64 = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            externalPaymentId: $data['externalPaymentId'] ?? $data['external_payment_id'],
            status: $data['status'],
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            dueDate: $data['dueDate'] ?? $data['due_date'] ?? null,
            billingType: $data['billingType'] ?? $data['billing_type'] ?? null,
            invoiceUrl: $data['invoiceUrl'] ?? $data['invoice_url'] ?? null,
            pixCode: $data['pixCode'] ?? $data['pix_code'] ?? null,
            barCode: $data['barCode'] ?? $data['bar_code'] ?? null,
            expiresAt: $data['expiresAt'] ?? $data['expires_at'] ?? null,
            qrCodeBase64: $data['qrCodeBase64'] ?? $data['qr_code_base64'] ?? null,
        );
    }
}

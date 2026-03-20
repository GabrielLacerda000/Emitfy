<?php

namespace App\Actions\Payments;

use App\Dto\Payments\ChargeData;
use App\Enums\BillingType;
use App\Enums\PaymentStatus;
use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;

class CreatePixChargeAction
{
    public function execute(Subscription $subscription): SubscriptionPayment
    {
        $provider = $subscription->activeProvider;
        $gateway  = PaymentGatewayFactory::make($provider->provider);

        $price = $subscription->billing_cycle === 'yearly'
            ? $subscription->plan->price_yearly
            : $subscription->plan->price_monthly;

        $data = new ChargeData(
            customerId:    $provider->provider_customer_id,
            amount:        (float) $price,
            currency:      'BRL',
            description:   "Subscription - {$subscription->plan->name}",
            dueDate:       now()->addDay()->toDateString(),
            paymentMethod: BillingType::Pix->value,
        );

        $response = $gateway->charge($data);

        $status = $this->normalizeStatus($response->status);

        $provider->update([
            'provider_payment_id' => $response->externalPaymentId,
            'status'              => $status->value,
            'metadata'            => array_merge($provider->metadata ?? [], [
                'pix_code'    => $response->pixCode,
                'invoice_url' => $response->invoiceUrl,
            ]),
        ]);

        $payment = SubscriptionPayment::create([
            'subscription_id'     => $subscription->id,
            'provider'            => $provider->provider,
            'external_payment_id' => $response->externalPaymentId,
            'amount'              => $price,
            'status'              => $status,
            'paid_at'             => $status === PaymentStatus::PAID ? now() : null,
            'raw_payload'         => (array) $response,
        ]);

        // webhook concern
        // $subscription->update(['status' => $status->value]);

        return $payment;
    }

    private function normalizeStatus(string $status): PaymentStatus
    {
        return match ($status) {
            'paid', 'CONFIRMED', 'RECEIVED', 'completed' => PaymentStatus::PAID,
            'overdue', 'OVERDUE'            => PaymentStatus::EXPIRED,
            'cancelled', 'CANCELLED'        => PaymentStatus::FAILED,
            default                         => PaymentStatus::PENDING,
        };
    }
}

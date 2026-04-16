<?php

namespace App\Actions\Payments;

use App\Dto\Payments\ChargeData;
use App\Dto\Payments\CustomerData;
use App\Enums\PaymentStatus;
use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;

class CreatePixChargeAction
{
    public function execute(Subscription $subscription): SubscriptionPayment
    {
        $provider = $subscription->activeProvider;

        if (! $provider) {
            throw new \RuntimeException(
                "Subscription {$subscription->id} has no active provider. Call CreateSubscriptionProviderAction first."
            );
        }

        $gateway = PaymentGatewayFactory::make($provider->provider);

        $customer = new CustomerData(
            name:     $subscription->user->name,
            email:    $subscription->user->email,
            document: $subscription->user->document,
            phone:    null,
        );

        $price = $subscription->billing_cycle === 'yearly'
            ? $subscription->plan->price_yearly
            : $subscription->plan->price_monthly;

        $data = new ChargeData(
            customer:    $customer,
            amount:      (float) $price,
            currency:    'BRL',
            description: "Subscription - {$subscription->plan->name}",
            dueDate:     now()->addDay()->toDateString(),
            metadata:    ['externalReference' => "sub:{$subscription->id}"],
        );

        $response = $gateway->charge($data);

        $status = $gateway->mapStatus($response->status);

        return SubscriptionPayment::create([
            'subscription_id'     => $subscription->id,
            'provider'            => $provider->provider,
            'external_payment_id' => $response->externalPaymentId,
            'amount'              => $price,
            'status'              => $status,
            'paid_at'             => $status === PaymentStatus::PAID ? now() : null,
            'pix_code'            => $response->pixCode,
            'qr_code_base64'      => $response->qrCodeBase64,
            'expires_at'          => $response->expiresAt,
            'raw_payload'         => [
                'externalPaymentId' => $response->externalPaymentId,
                'status'            => $response->status,
                'amount'            => $response->amount,
                'expiresAt'         => $response->expiresAt,
            ],
        ]);
    }
}

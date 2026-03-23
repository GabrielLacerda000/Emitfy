<?php

namespace App\Actions\Payments;

use App\Dto\Payments\ChargeData;
use App\Enums\BillingType;
use App\Enums\PaymentStatus;
use App\Factories\PaymentGatewayFactory;
use App\Gateways\PaguedevGateway;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;

class CreatePixChargeAction
{
    public function execute(Subscription $subscription): SubscriptionPayment
    {
        $provider = $subscription->activeProvider;

        /** @var PaguedevGateway $gateway */
        $gateway = PaymentGatewayFactory::make($provider->provider);

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
            metadata:      ['externalReference' => "sub:{$subscription->id}"],
        );

        $response = $gateway->charge($data);

        $status = $gateway->mapStatus($response->status);

        $payment = SubscriptionPayment::create([
            'subscription_id'     => $subscription->id,
            'provider'            => $provider->provider,
            'external_payment_id' => $response->externalPaymentId,
            'amount'              => $price,
            'status'              => $status,
            'paid_at'             => $status === PaymentStatus::PAID ? now() : null,
            'raw_payload'         => (array) $response,
        ]);


        return $payment;
    }
}

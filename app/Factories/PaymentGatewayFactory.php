<?php

namespace App\Factories;

use App\Gateways\AsaasGateway;
use App\Gateways\PagarDevGateway;
use App\Interfaces\Payments\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'asaas' => app(AsaasGateway::class),
            'pagar_dev' => app(PagarDevGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$provider}"),
        };
    }
}

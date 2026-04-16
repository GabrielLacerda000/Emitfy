<?php

namespace App\Factories;

use App\Gateways\AsaasGateway;
use App\Gateways\PaguedevGateway;
use App\Interfaces\Payments\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'asaas' => app(AsaasGateway::class),
            'pague_dev' => app(PaguedevGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$provider}"),
        };
    }
}

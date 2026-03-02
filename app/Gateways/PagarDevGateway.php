<?php

namespace App\Gateways;

use App\Interfaces\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PagarDevGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.pagar_dev.base_url');
        $this->apiKey = config('services.pagar_dev.api_key');
    }

    public function createSubscription(array $data): array
    {
        // TODO: implement when PagarDev API docs are available
        // $response = Http::withToken($this->apiKey)
        //     ->post("{$this->baseUrl}/subscriptions", $data)
        //     ->throw()
        //     ->json();

        throw new \RuntimeException('PagarDev gateway not yet implemented.');
    }

    public function charge(array $data): array
    {
        // TODO: implement when PagarDev API docs are available
        throw new \RuntimeException('PagarDev gateway not yet implemented.');
    }

    public function cancelSubscription(string $externalId): bool
    {
        // TODO: implement when PagarDev API docs are available
        throw new \RuntimeException('PagarDev gateway not yet implemented.');
    }

    public function refund(string $paymentId): bool
    {
        // TODO: implement when PagarDev API docs are available
        throw new \RuntimeException('PagarDev gateway not yet implemented.');
    }
}

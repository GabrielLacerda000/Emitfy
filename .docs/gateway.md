Abaixo está um **plano técnico em Markdown**, estruturado para você usar como base de implementação com LLMs no seu projeto **Laravel + Vue + Inertia**, mantendo sua arquitetura baseada em **Actions** e preparando o sistema para múltiplos gateways (Asaas cartão, Pagar.dev PIX e futuro Stripe).

---

# 📌 Plano de Implementação — Camada de Payment Gateway Intercambiável

## 🎯 Objetivo

Criar uma arquitetura desacoplada que permita:

- Usar múltiplos gateways simultaneamente
- Trocar gateway sem alterar regra de negócio
- Adicionar novos gateways (ex: Stripe) sem refatoração estrutural
- Manter padrão baseado em Actions
- Centralizar regras de assinatura no seu sistema (não no gateway)

---

# 🧱 1. Estrutura de Pastas

Dentro de `app/`:

```
app/
│
├── Interfaces/
│   └── Payments/
│       └── PaymentGatewayInterface.php
│
├── Factories/
│   └── PaymentGatewayFactory.php
│
├── Gateways/
│   ├── AsaasGateway.php
│   ├── Paguedev.php
│   └── StripeGateway.php (futuro)
│
├── Actions/
│   └── Payments/
│       ├── CreateCardSubscriptionAction.php
│       ├── ChargeSubscriptionAction.php
│       └── CancelSubscriptionAction.php
```

---

# 🧠 2. Conceito Arquitetural

## Camadas

### 1️⃣ Actions

Responsáveis por orquestrar o fluxo de negócio.

Não sabem qual gateway está sendo usado.

---

### 2️⃣ PaymentGatewayInterface

Define o contrato comum para qualquer gateway.

Todos devem implementar os mesmos métodos.

---

### 3️⃣ Gateways (Implementações concretas)

Cada classe sabe falar com a API específica.

Ex:

- Asaas usa REST
- Pagar.dev usa outro formato
- Stripe tem SDK próprio

---

### 4️⃣ Factory

Responsável por devolver a implementação correta baseada no provider salvo no banco.

---

# 🧾 3. Interface Base

Arquivo:
`app/Interfaces/Payments/PaymentGatewayInterface.php`

```php
<?php

namespace App\Interfaces\Payments;

interface PaymentGatewayInterface
{
    public function createSubscription(array $data): array;

    public function charge(array $data): array;

    public function cancelSubscription(string $externalId): bool;

    public function refund(string $paymentId): bool;
}
```

🔎 Observação importante:
Sempre retorne dados normalizados.
Nunca retorne a resposta bruta da API.

---

# 🏭 4. Factory

Arquivo:
`app/Factories/PaymentGatewayFactory.php`

```php
<?php

namespace App\Factories;

use App\Gateways\AsaasGateway;
use App\Gateways\Paguedev;
use App\Gateways\StripeGateway;
use App\Interfaces\Payments\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'asaas' => app(AsaasGateway::class),
            'pagar_dev' => app(Paguedev::class),
            'stripe' => app(StripeGateway::class),
            default => throw new InvalidArgumentException("Gateway não suportado"),
        };
    }
}
```

---

# 💳 5. Gateway Concreto (Exemplo Asaas)

Arquivo:
`app/Gateways/AsaasGateway.php`

```php
<?php

namespace App\Gateways;

use App\Interfaces\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class AsaasGateway implements PaymentGatewayInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.base_url');
        $this->apiKey = config('services.asaas.api_key');
    }

    public function createSubscription(array $data): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/subscriptions", $data);

        return [
            'external_id' => $response['id'],
            'status' => $response['status'],
        ];
    }

    public function charge(array $data): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/payments", $data);

        return [
            'external_id' => $response['id'],
            'status' => $response['status'],
        ];
    }

    public function cancelSubscription(string $externalId): bool
    {
        Http::withToken($this->apiKey)
            ->delete("{$this->baseUrl}/subscriptions/{$externalId}");

        return true;
    }

    public function refund(string $paymentId): bool
    {
        Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/payments/{$paymentId}/refund");

        return true;
    }
}
```

---

# ⚙️ 6. Action de Criação de Assinatura

Arquivo:
`app/Actions/Payments/CreateSubscriptionAction.php`

```php
<?php

namespace App\Actions\Payments;

use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;

class CreateCardSubscriptionAction
{
    public function execute(Subscription $subscription)
    {
        $gateway = PaymentGatewayFactory::make(
            $subscription->subscriptionProvider->provider
        );

        $response = $gateway->createSubscription([
            'customer' => $subscription->user->gateway_customer_id,
            'value' => $subscription->plan->price,
            'cycle' => 'MONTHLY',
        ]);

        $subscription->update([
            'external_id' => $response['external_id'],
            'status' => $response['status'],
        ]);

        return $subscription;
    }
}
```

---

# 🗄️ 7. Modelagem Esperada no Banco

### subscriptions

Assinatura do usuário no seu sistema

```
id
user_id
plan_id
status
external_id
subscription_provider_id
```

---

### subscription_providers

```
id
subscription_id
provider (asaas, pagar_dev, stripe)
external_id
metadata (json)
```

---

### subscription_payments

Histórico de cobranças

```
id
subscription_id
provider
external_payment_id
amount
status
paid_at
raw_payload (json)
```

---

# 🔁 8. Fluxo Completo

1. Usuário escolhe plano (Vue/Inertia)
2. Controller chama `CreateSubscriptionAction`
3. Action chama Factory
4. Factory devolve gateway correto
5. Gateway cria assinatura na API externa
6. Seu sistema salva `external_id`
7. Webhook atualiza status depois

---

# 🌍 9. Suporte a Múltiplos Gateways no Futuro

Para adicionar Stripe:

1. Criar `StripeGateway.php`
2. Implementar `PaymentGatewayInterface`
3. Registrar na Factory
4. Adicionar config no `services.php`

Nenhuma Action precisa ser alterada.

---

# 📌 10. Ponto Estratégico Importante

Sua regra de negócio NUNCA deve depender:

- de nome de campo da API
- de status bruto do gateway
- de payload original

Sempre normalize.

---

# 🔥 11. Observação Arquitetural Crítica

Não delegue a lógica de planos ao gateway.

O gateway:

- só cobra
- só cria assinatura técnica

Quem decide:

- upgrade
- downgrade
- período de teste
- cancelamento futuro

É o seu sistema.

---

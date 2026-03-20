# Plano de Implementação — PagarDev (Pix Dinâmico)

> **Escopo atual:** apenas `charge()` com Pix dinâmico.
> Os demais métodos da interface continuam como stubs lançando `RuntimeException`.

---

## ⚠️ 1. Dinâmico vs Estático — decisão crítica

### ❌ QR Code Estático

- mesmo código sempre
- valor pode ser alterado manualmente
- **não tem identificação forte de pagamento**
- difícil conciliar automaticamente
- ruim para SaaS

👉 bom para: doações, pagamentos manuais

---

### ✅ QR Code Dinâmico (**o que deve ser usado**)

- cada cobrança gera um QR único
- tem `transaction_id`
- permite webhook confiável
- tem expiração
- ideal para automação

👉 bom para: SaaS, recorrência, controle de estado

**Decisão:** USE QR Code dinâmico. Com estático não é possível saber com segurança qual assinatura foi paga.

---

## 🧠 2. Enum de status — `App\Enums\PaymentStatus`

> Usar `PaymentStatus` (não `SubscriptionPaymentStatus`) para evitar confusão com o nome do model.

Criar em `app/Enums/PaymentStatus.php`:

```php
namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING  = 'pending';
    case PAID     = 'paid';
    case FAILED   = 'failed';
    case EXPIRED  = 'expired';
    case CANCELED = 'canceled';
    case REFUNDED = 'refunded';
}
```

---

## 🔄 3. Mapeamento de status (no gateway)

Nunca use status direto da API. O método retorna `PaymentStatus`.

```php
private function normalizeStatus(string $status): PaymentStatus
{
    return match ($status) {
        'paid', 'approved', 'completed'  => PaymentStatus::PAID,
        'pending', 'waiting_payment'     => PaymentStatus::PENDING,
        'expired'                        => PaymentStatus::EXPIRED,
        'canceled', 'cancelled'          => PaymentStatus::CANCELED,
        default                          => PaymentStatus::FAILED,
    };
}
```

> Ao gravar em `ChargeResponse->status` (que é `string`), use `.value`:
>
> ```php
> status: $this->normalizeStatus($response['status'] ?? 'pending')->value,
> ```

---

## 🧱 4. DTOs existentes — nenhum novo DTO necessário

Ambos já existem e devem ser usados como estão.

### `App\Dto\Payments\ChargeData` (`app/Dto/Payments/ChargeData.php`)

| Campo            | Tipo      | Notas                                            |
| ---------------- | --------- | ------------------------------------------------ |
| `$customerId`    | `string`  | ID do cliente no gateway                         |
| `$amount`        | `float`   | Valor em reais — converter `* 100` para centavos |
| `$currency`      | `string`  | ex: `'BRL'`                                      |
| `$description`   | `string`  |                                                  |
| `$dueDate`       | `?string` |                                                  |
| `$paymentMethod` | `?string` | ex: `'pix'`                                      |
| `$metadata`      | `array`   |                                                  |

### `App\Dto\Payments\ChargeResponse` (`app/Dto/Payments/ChargeResponse.php`)

| Campo                | Tipo      | Notas                               |
| -------------------- | --------- | ----------------------------------- |
| `$externalPaymentId` | `string`  | `transaction_id` retornado pelo SDK |
| `$status`            | `string`  | Gravar `.value` do `PaymentStatus`  |
| `$amount`            | `?float`  |                                     |
| `$dueDate`           | `?string` |                                     |
| `$billingType`       | `?string` |                                     |
| `$invoiceUrl`        | `?string` |                                     |
| `$pixCode`           | `?string` | Copia-e-cola Pix                    |
| `$barCode`           | `?string` |                                     |

---

## 🎯 5. Action — `ChargePixAction`

> A **lógica de negócio** vive aqui, não no gateway.
> O gateway só formata o payload e faz a chamada HTTP/SDK.
> Padrão espelhado em `app/Actions/Payments/ChargeSubscriptionAction.php`.

Arquivo: `app/Actions/Payments/ChargePixAction.php`

```php
namespace App\Actions\Payments;

use App\Dto\Payments\ChargeData;
use App\Enums\PaymentStatus;
use App\Factories\PaymentGatewayFactory;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;

class ChargePixAction
{
    public function execute(Subscription $subscription, float $amount): SubscriptionPayment
    {
        $provider = $subscription->activeProvider;

        $gateway = PaymentGatewayFactory::make($provider->provider);

        $chargeData = new ChargeData(
            customerId:    $provider->provider_customer_id,
            amount:        $amount,
            currency:      'BRL',
            description:   "Assinatura #{$subscription->id}",
            dueDate:       now()->toDateString(),
            paymentMethod: 'pix',
        );

        $response = $gateway->charge($chargeData);

        return SubscriptionPayment::create([
            'subscription_id'     => $subscription->id,
            'provider'            => $provider->provider,
            'external_payment_id' => $response->externalPaymentId,
            'amount'              => $amount,
            'status'              => $response->status,            // já é string (.value)
            'paid_at'             => $response->status === PaymentStatus::PAID->value ? now() : null,
            'pix_code'            => $response->pixCode,
            'invoice_url'         => $response->invoiceUrl,
        ]);
    }
}
```

**Separação de responsabilidades:**

- `ChargePixAction` = orquestração (montar DTO, chamar gateway, persistir)
- `Paguedev::charge()` = transporte (formatar payload, chamar SDK, mapear resposta)

---

## ⚙️ 6. Implementação do `Paguedev::charge()`

> SDK já instalado: `mountbit/pague-dev-php-sdk`
> Config já configurada: `config('services.pagar_dev.api_key')` e `config('services.pagar_dev.base_url')`
> **Sem lógica de negócio aqui** — apenas adapter HTTP/SDK. Padrão espelhado em `AsaasGateway::charge()`.

Arquivo: `app/Gateways/PaguedevGateway.php`

```php
use App\Enums\PaymentStatus;
use MountBit\PagueDev\Client;

public function charge(ChargeData $data): ChargeResponse
{
    $client = new Client($this->apiKey);

    $response = $client->pix()->create(array_filter([
        'customerId'  => $data->customerId,
        'amount'      => (int) ($data->amount * 100), // reais → centavos
        'description' => $data->description,
        'dueDate'     => $data->dueDate,
        'metadata'    => $data->metadata ?: null,
    ], fn ($v) => $v !== null));

    return new ChargeResponse(
        externalPaymentId: $response['transaction_id'],
        status:            $this->normalizeStatus($response['status'] ?? 'pending'),
        amount:            $data->amount,
        dueDate:           $data->dueDate,
        billingType:       'pix',
        invoiceUrl:        $response['invoice_url'] ?? null,
        pixCode:           $response['pix_code'] ?? null,
    );
}
```

> `normalizeStatus()` retorna **string** (`.value` já aplicado), consistente com `ChargeResponse->status: string`.
> Os demais métodos (`createCustomer`, `tokenizeCreditCard`, `createSubscription`, `cancelSubscription`, `refund`) continuam lançando `RuntimeException` — não implementar agora.

---

## 🧾 7. Persistência (`SubscriptionPayment`)

> A persistência acontece **dentro de `ChargePixAction::execute()`** (seção 5), não diretamente no gateway.
> Campos mapeados do `ChargeResponse`:

| Campo do model        | Fonte                                                            |
| --------------------- | ---------------------------------------------------------------- |
| `subscription_id`     | `$subscription->id`                                              |
| `provider`            | `$provider->provider`                                            |
| `external_payment_id` | `$response->externalPaymentId`                                   |
| `amount`              | `$amount` (parâmetro da action)                                  |
| `status`              | `$response->status` (já string via `.value`)                     |
| `paid_at`             | `now()` se `status === PaymentStatus::PAID->value`, senão `null` |
| `pix_code`            | `$response->pixCode`                                             |
| `invoice_url`         | `$response->invoiceUrl`                                          |

> `raw_payload` não existe em `ChargeResponse` — gravar `$response` original diretamente no gateway se rastreabilidade for necessária.

---

## 🔔 8. Webhook (trabalho futuro)

```
POST /webhooks/pague-dev
```

Fluxo:

```php
$payload   = request()->all();
$paymentId = $payload['transaction_id'];
$payment   = SubscriptionPayment::where('external_payment_id', $paymentId)->first();
$status    = $this->normalizeStatus($payload['status']);

$payment->update([
    'status'  => $status->value,
    'paid_at' => $status === PaymentStatus::PAID ? now() : null,
]);
```

---

## 🔁 9. Atualizar Subscription ao receber pagamento (trabalho futuro)

```php
if ($status === PaymentStatus::PAID) {
    $subscription = $payment->subscription;

    $next = match ($subscription->billing_cycle) {
        'monthly' => now()->addMonth(),
        'yearly'  => now()->addYear(),
    };

    $subscription->update([
        'status'             => 'active',
        'current_period_end' => $next,
        'next_billing_at'    => $next,
    ]);
}
```

---

## ⏱️ 10. Command (cron) — trabalho futuro

Rodar a cada 1–5 minutos:

```php
Subscription::where('status', 'active')
    ->where('next_billing_at', '<=', now())
    ->each(function ($subscription) {
        $hasPending = $subscription->subscriptionPayments()
            ->where('status', PaymentStatus::PENDING->value)
            ->exists();

        if ($hasPending) return;

        app(ChargeSubscriptionAction::class)
            ->execute($subscription, $subscription->plan->price_monthly);

        $subscription->update([
            'next_billing_at' => now()->addMonth(),
        ]);
    });
```

---

## 🧠 11. Insight

Você não está implementando "Pix".
Você está implementando um motor de cobrança com estados, tentativas e reconciliação.
Pix é só o meio de pagamento.

---

## ✔️ Resumo

| Item                            | Status                                              |
| ------------------------------- | --------------------------------------------------- |
| QR Code dinâmico                | ✅ decisão tomada                                   |
| `PaymentStatus` enum            | 🔲 criar `app/Enums/PaymentStatus.php`              |
| `normalizeStatus()`             | 🔲 implementar no gateway                           |
| `ChargePixAction`               | 🔲 criar `app/Actions/Payments/ChargePixAction.php` |
| `charge()` com SDK PagarDev     | 🔲 implementar (thin adapter)                       |
| `ChargeData` / `ChargeResponse` | ✅ já existem — não criar novos                     |
| Webhook                         | 🔲 trabalho futuro (seção 8)                        |
| Cron de recorrência             | 🔲 trabalho futuro (seção 10)                       |

> Nunca dependa do gateway como fonte de verdade.
> Próximo passo após `charge()`: modelar **expiração + reemissão automática de Pix**.

## o fluxo é este

[1] Usuário faz request
↓
[2] Controller
↓
[3] CreateSubscriptionAction (local, banco)
↓
[4] CreatePixChargeAction (gateway)
↓
[5] Retorna QR Code
↓
[6] Usuário paga
↓
[7] Webhook do gateway
↓
[8] Atualiza Payment + Subscription

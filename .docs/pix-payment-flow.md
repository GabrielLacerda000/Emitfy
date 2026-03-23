Este é um documento técnico sólido, mas para que ele funcione como uma verdadeira **Single Source of Truth (SSoT)** para o seu sistema, podemos elevar a organização usando uma estrutura de documentação de arquitetura mais moderna (estilo _Architectural Decision Records_ misturado com _Technical Specs_).

Vou reorganizar o conteúdo focando em **escaneabilidade**, utilizando os padrões que você prefere (como callouts de Obsidian e separação clara de conceitos).

---

# 📑 PIX Payment Flow — Subscription Billing

## 🎯 Abstract

> [!ABSTRACT]
> 
> Documentação técnica do fluxo de pagamento recorrente via PIX. O sistema opera em dois ciclos independentes (Síncrono e Assíncrono) para garantir resiliência contra latência de rede e processamento por parte do gateway (**pague.dev**).

**Tags:** #Payments #Architecture #Laravel #PIX #Webhooks

---

## 🏗️ Conceitos-Chave

Antes de detalhar os passos, é preciso entender as entidades fundamentais:

- **Cycle 1 (Charge):** O "gatilho" do usuário. É a intenção de compra que gera o QR Code.
    
- **Cycle 2 (Webhook):** A "verdade" financeira. É onde o dinheiro é confirmado e o serviço liberado.
    
- **Idempotência:** Garantia de que o mesmo evento de pagamento não ative a assinatura duas vezes.
    
- **Assinatura PENDING:** Estado inicial onde o registro existe no DB, mas o acesso ainda não foi concedido.
    

---

## 🔄 O Fluxo de Execução

## ⚡ Ciclo 1: Charge (Síncrono)

Responsável por registrar a intenção e fornecer o meio de pagamento ao cliente.

1. **Orquestração inicial:** O Controller recebe o plano/ciclo e delega para as `Actions`.
    
2. **Persistência de Intenção:** * `CreateSubscriptionAction`: Cria o registro pai (`PENDING`).
    
    - `CreateSubscriptionProviderAction`: Vincula ao gateway atual.
        
3. **Geração do QR Code:**
    
    - O `CreatePixChargeAction` utiliza a `PaymentGatewayFactory` para resolver o driver.
        
    - Envia um `ChargeData` (DTO) para o Gateway.
        
    - O `PaguedevGateway` comunica-se com a API externa e retorna os dados do PIX.
        
4. **Resposta ao Cliente:** O sistema salva o `external_payment_id` e devolve o `qrCodeBase64` e `pixCopyPaste`.
    

## ⚓ Ciclo 2: Webhook (Assíncrono)

Onde ocorre a mutação de estado baseada na confirmação do gateway.

1. **Recebimento e Validação:**
    
    - O `PagueDevWebhookController` valida a assinatura HMAC-SHA256 (segurança crítica).
        
    - Retorna `200 OK` imediatamente para o gateway (libera o socket).
        
2. **Processamento em Fila:**
    
    - O Job `ProcessPagueDevWebhook` é disparado.
        
    - **Check de Idempotência:** Verifica se o `eventId` já existe no `raw_payload`.
        
3. **Ativação:** * Mapeia o status externo para o `PaymentStatus` interno.
    
    - Se `PAID`, altera a `Subscription` para `ACTIVE` e calcula a data de expiração (`+30` ou `+365` dias).
        

---

## 🛠️ Detalhes de Implementação & Segurança

## 🔐 Verificação de Assinatura

> [!DANGER] Segurança Crítica
> 
> Nunca utilize `$request->all()` para validar a assinatura. O HMAC deve ser calculado sobre o **raw body** (bytes puros) para evitar inconsistências de serialização JSON.

PHP

```
// Lógica central no Controller
$signingKey = hex2bin(hash('sha256', config('services.pague_dev.secret')));
$computed = hash_hmac('sha256', $request->getContent(), $signingKey);

if (!hash_equals($computed, $request->header('X-Webhook-Signature'))) {
    abort(400, 'Invalid Signature');
}
```

## 🔁 Estratégia de Busca de Pagamento (Fallback)

Para evitar falhas caso o webhook chegue antes da conclusão do Ciclo 1 no banco de dados:

1. **Primário:** Busca por `external_payment_id`.
    
2. **Secundário:** Parse do `externalReference` (ex: `sub:42`) para localizar via ID interno.
    

---

## 📊 Matriz de Estados

## Payment Status (`PaymentStatus.php`)

|**Status**|**Trigger**|**Significado**|
|---|---|---|
|`PENDING`|Charge Created|Aguardando pagamento|
|`PAID`|`payment_completed`|Sucesso financeiro|
|`EXPIRED`|`payment_expired`|Tempo limite do QR Code atingido|
|`FAILED`|`payment_cancelled`|Cancelado ou recusado|

## Subscription Status (`SubscriptionStatus.php`)

|**Status**|**Condição de Entrada**|**Impacto no Usuário**|
|---|---|---|
|`PENDING`|Criação do registro|Sem acesso|
|`ACTIVE`|Webhook `PAID`|Acesso liberado|
|`OVERDUE`|Expiração sem renovação|Acesso suspenso (Grace Period)|
|`CANCELLED`|Cancelamento explícito|Acesso removido|

---

## 💡 Insights e Nuances

> [!INFO] Por que Actions e DTOs?
> 
> - **Desacoplamento:** O Gateway não sabe que existe um banco de dados. Ele apenas transforma "Request em Response".
>     
> - **Testabilidade:** Você pode testar o `CreatePixChargeAction` mockando apenas a interface do Gateway, sem precisar de HTTP real.
>     
> - **Resiliência:** O uso de filas no Ciclo 2 isola falhas. Se o seu DB travar, o gateway não saberá; o Job tentará novamente em 30 segundos (Exponential Backoff).
>     

---

## 📂 Referência de Arquivos Rápidos

|**Camada**|**Caminho**|
|---|---|
|**Logic (Actions)**|`app/Actions/Payments/`|
|**Gateway Drivers**|`app/Gateways/`|
|**Contracts**|`app/Interfaces/Payments/`|
|**Payloads (DTOs)**|`app/Dto/Payments/`|
|**Entry Points**|`app/Http/Controllers/Webhooks/`|
|**Background Jobs**|`app/Jobs/`|

---

**Gostaria que eu gerasse os templates das classes principais (como o DTO ou a Action de orquestração) baseados nessa nova estrutura?**
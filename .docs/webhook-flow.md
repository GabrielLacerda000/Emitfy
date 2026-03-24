 How the Webhook Works

  1. Entry Point — HTTP Route (routes/api.php)

  POST /api/webhooks/paguedev
  PagueDev (the payment gateway) calls this URL whenever a payment event happens (e.g., a PIX was paid, expired, etc.).

  ---
  2. Controller — PagueDevWebhookController

  Before doing anything, it verifies the signature:
  - PagueDev sends an X-Webhook-Signature header (HMAC-SHA256 of the raw body).
  - The controller recomputes the expected signature using your webhook_secret from config.
  - If signatures don't match → returns 400 immediately (rejects forged requests).

  If valid → dispatches ProcessPagueDevWebhook::dispatch(payload) to the queue and immediately returns 200 OK to
  PagueDev (fast response, no blocking).

  ---
  3. Job — ProcessPagueDevWebhook (async, queued)

  This runs in the background. Steps:

  1. Idempotency check — if the eventId was already stored in a payment's raw_payload, skip it. Prevents
  double-processing if PagueDev retries.
  2. Find the payment — looks up SubscriptionPayment by external_payment_id. Falls back to externalReference (format:
  sub:123) if not found directly.
  3. Map the status — calls $gateway->mapStatus($event) to translate PagueDev's event name (e.g., payment.confirmed)
  into your internal PaymentStatus enum.
  4. Update the payment — saves new status, sets paid_at if paid, and stores the raw webhook data.
  5. Update the provider — updates SubscriptionProvider with the latest payment ID and status.
  6. Activate the subscription — if status is PAID, sets subscription to ACTIVE and extends current_period_end by 30 or
  365 days depending on billing cycle.

  ---
  Flow Diagram

  PagueDev → POST /api/webhooks/paguedev
                │
                ├─ verify HMAC signature
                │     └─ fail → 400
                │
                ├─ dispatch job to queue → 200 OK (instant)
                │
                └─ [queue worker picks up job]
                      ├─ idempotency check
                      ├─ find SubscriptionPayment
                      ├─ map event → PaymentStatus
                      ├─ update payment
                      ├─ update provider
                      └─ if PAID → activate subscription

  The key design choice is async processing: the controller responds to PagueDev immediately, and all the business logic
   runs in the background queue. This prevents timeouts and allows retries (up to 5 attempts with exponential backoff)
  if something fails.
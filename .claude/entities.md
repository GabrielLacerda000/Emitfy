# SimpleInvoice - Data Models

This document defines the core database entities currently represented in migrations.

---

## 1. Users

Represents the account owner.

| Field      | Type      | Notes               |
| ---------- | --------- | ------------------- |
| id         | UUID      | Primary key         |
| name       | string    |                     |
| email      | string    | Unique              |
| password   | string    | Hashed              |
| currency   | string    | Default user locale |
| logo_url   | string    | Nullable            |
| created_at | timestamp |                     |
| updated_at | timestamp |                     |
| bypass_billing | boolean | Nullable            |

---

## 2. Clients

Represents invoice recipients per user.

| Field        | Type      | Notes              |
| ------------ | --------- | ------------------ |
| id           | UUID      | Primary key        |
| user_id      | UUID      | FK -> users.id     |
| name         | string    |                    |
| email        | string    |                    |
| company_name | string    | Nullable           |
| notes        | text      | Nullable           |
| created_at   | timestamp |                    |
| updated_at   | timestamp |                    |

---

## 3. Invoices

Represents invoice documents and lifecycle state.

| Field        | Type      | Notes                       |
| ------------ | --------- | --------------------------- |
| id           | bigint    | Primary key                 |
| user_id      | UUID      | FK -> users.id              |
| client_id    | bigint    | FK                          |
| number       | string    | Invoice number              |
| status       | string    | draft, sent, paid, overdue  |
| issue_date   | date      |                             |
| due_date     | date      |                             |
| subtotal     | decimal   |                             |
| tax          | decimal   |                             |
| total        | decimal   |                             |
| notes        | text      | Nullable                    |
| public_token | string    | Public invoice access token |
| sent_at      | timestamp | Nullable                    |
| paid_at      | timestamp | Nullable                    |
| created_at   | timestamp |                             |
| updated_at   | timestamp |                             |

---

## 4. Invoice Items

Line items attached to invoices.

| Field       | Type      | Notes             |
| ----------- | --------- | ----------------- |
| id          | bigint    | Primary key       |
| invoice_id  | bigint    | FK -> invoices.id |
| description | string    |                   |
| quantity    | integer   |                   |
| unit_price  | decimal   |                   |
| total       | decimal   |                   |
| created_at  | timestamp |                   |
| updated_at  | timestamp |                   |

---

## 5. Payments

Invoice payment records.

| Field               | Type      | Notes              |
| ------------------- | --------- | ------------------ |
| id                  | bigint    | Primary key        |
| invoice_id          | bigint    | FK -> invoices.id  |
| provider            | string    | Gateway identifier |
| provider_payment_id | string    | External reference |
| amount              | decimal   |                    |
| status              | string    |                    |
| paid_at             | timestamp | Nullable           |
| created_at          | timestamp |                    |
| updated_at          | timestamp |                    |

---

## 6. Reminder Schedules

Scheduled invoice reminder records.

| Field       | Type      | Notes                    |
| ----------- | --------- | ------------------------ |
| id          | bigint    | Primary key              |
| invoice_id  | bigint    | FK -> invoices.id        |
| type        | string    | before_due, on_due, etc. |
| offset_days | integer   |                          |
| sent_at     | timestamp | Nullable                 |
| created_at  | timestamp |                          |
| updated_at  | timestamp |                          |

---

## 7. Plans

Subscription plan catalog.

| Field         | Type      | Notes       |
| ------------- | --------- | ----------- |
| id            | bigint    | Primary key |
| name          | string    |             |
| price_monthly | decimal   |             |
| price_yearly  | decimal   |             |
| max_clients   | integer   |             |
| max_invoices  | integer   |             |
| created_at    | timestamp |             |
| updated_at    | timestamp |             |

---

## 8. Subscriptions

User subscription state.

| Field              | Type      | Notes                    |
| ------------------ | --------- | ------------------------ |
| id                 | bigint    | Primary key              |
| user_id            | UUID      | FK -> users.id           |
| plan_id            | bigint    | FK -> plans.id           |
| status             | string    | active, cancelled, etc.  |
| billing_cycle      | string    | monthly or yearly        |
| current_period_end | timestamp | Nullable                 |
| created_at         | timestamp |                          |
| updated_at         | timestamp |                          |

---

## 9. Subscription Providers

External provider mapping for subscriptions.

| Field                    | Type      | Notes                    |
| ------------------------ | --------- | ------------------------ |
| id                       | bigint    | Primary key              |
| subscription_id          | bigint    | FK -> subscriptions.id   |
| provider                 | string    | asaas, pagar_dev, etc.   |
| provider_customer_id      | string    | Nullable external customer id     |
| provider_subscription_id  | string    | Nullable external sub id          |
| last_provider_payment_id  | string    | Nullable last provider payment id |
| status                   | string    |                          |
| metadata                 | json      | Nullable                 |
| created_at               | timestamp |                          |
| updated_at               | timestamp |                          |

---

## 10. Subscription Payments

Subscription payment history.

| Field               | Type      | Notes                  |
| ------------------- | --------- | ---------------------- |
| id                  | bigint    | Primary key            |
| subscription_id     | bigint    | FK -> subscriptions.id |
| provider            | string    |                        |
| external_payment_id | string    | Nullable               |
| amount              | decimal   |                        |
| status              | string    |                        |
| paid_at             | timestamp | Nullable               |
| raw_payload         | json      | Nullable               |
| created_at          | timestamp |                        |
| updated_at          | timestamp |                        |

---

## 11. Relationships Overview

- User has many clients.
- User has many invoices.
- User has many subscriptions.
- Client has many invoices.
- Invoice has many invoice items.
- Invoice has many payments.
- Invoice has many reminder schedules.
- Plan has many subscriptions.
- Subscription has many subscription providers.
- Subscription has many subscription payments.

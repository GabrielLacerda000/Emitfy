# SimpleInvoice – Data Models

This document defines the core database entities for the SimpleInvoice MVP.
The schema is optimized for simplicity, clarity, and fast iteration.

---

## 1. Users

Represents the freelancer account owner.

| Field      | Type      | Notes                       |
| ---------- | --------- | --------------------------- |
| id         | UUID      | Primary key                 |
| name       | string    | Freelancer full name        |
| email      | string    | Unique                      |
| password   | string    | Hashed                      |
| currency   | string    | Default currency (e.g. USD) |
| logo_url   | string    | Optional                    |
| created_at | timestamp |                             |
| updated_at | timestamp |                             |

---

## 2. Clients

Represents a client that receives invoices.

| Field        | Type      | Notes                 |
| ------------ | --------- | --------------------- |
| id           | UUID      | Primary key           |
| user_id      | UUID      | Owner (FK → users.id) |
| name         | string    | Client name           |
| email        | string    | Billing email         |
| company_name | string    | Optional              |
| notes        | text      | Optional              |
| created_at   | timestamp |                       |
| updated_at   | timestamp |                       |

---

## 3. Invoices

Represents an invoice document.

| Field        | Type      | Notes                         |
| ------------ | --------- | ----------------------------- |
| id           | UUID      | Primary key                   |
| user_id      | UUID      | Owner (FK → users.id)         |
| client_id    | UUID      | FK → clients.id               |
| number       | string    | Auto-generated invoice number |
| status       | enum      | draft, sent, paid, overdue    |
| issue_date   | date      |                               |
| due_date     | date      |                               |
| subtotal     | decimal   | Calculated                    |
| tax          | decimal   | Optional (MVP default = 0)    |
| total        | decimal   | Calculated                    |
| notes        | text      | Optional                      |
| public_token | string    | Used for public invoice link  |
| sent_at      | timestamp | Nullable                      |
| paid_at      | timestamp | Nullable                      |
| created_at   | timestamp |                               |
| updated_at   | timestamp |                               |

---

## 4. Invoice Items

Line items that compose an invoice.

| Field       | Type      | Notes                          |
| ----------- | --------- | ------------------------------ |
| id          | UUID      | Primary key                    |
| invoice_id  | UUID      | FK → invoices.id               |
| description | string    | Service or product description |
| quantity    | integer   | Default = 1                    |
| unit_price  | decimal   |                                |
| total       | decimal   | quantity × unit_price          |
| created_at  | timestamp |                                |
| updated_at  | timestamp |                                |

---

## 5. Payments

Tracks payment attempts and confirmations.

| Field               | Type      | Notes                      |
| ------------------- | --------- | -------------------------- |
| id                  | UUID      | Primary key                |
| invoice_id          | UUID      | FK → invoices.id           |
| provider            | enum      | stripe, paypal             |
| provider_payment_id | string    | External reference         |
| amount              | decimal   | Paid amount                |
| status              | enum      | pending, completed, failed |
| paid_at             | timestamp | Nullable                   |
| created_at          | timestamp |                            |
| updated_at          | timestamp |                            |

---

## 6. Reminder Schedules

Defines automated reminder rules for invoices.

| Field       | Type      | Notes                         |
| ----------- | --------- | ----------------------------- |
| id          | UUID      | Primary key                   |
| invoice_id  | UUID      | FK → invoices.id              |
| type        | enum      | before_due, on_due, after_due |
| offset_days | integer   | -3, 0, +7                     |
| sent_at     | timestamp | Nullable                      |
| created_at  | timestamp |                               |
| updated_at  | timestamp |                               |

---

## 7. Subscriptions

Tracks user subscription plans.

| Field                    | Type      | Notes                      |
| ------------------------ | --------- | -------------------------- |
| id                       | UUID      | Primary key                |
| user_id                  | UUID      | FK → users.id              |
| plan                     | enum      | free, pro, business        |
| provider                 | enum      | stripe                     |
| provider_subscription_id | string    | External reference         |
| status                   | enum      | active, canceled, past_due |
| current_period_end       | timestamp |                            |
| created_at               | timestamp |                            |
| updated_at               | timestamp |                            |

---

## 8. Relationships Overview

* User has many Clients
* User has many Invoices
* Client has many Invoices
* Invoice has many Invoice Items
* Invoice has many Payments
* Invoice has many Reminder Schedules
* User has one Subscription

---

## 9. MVP Simplifications

* Single currency per user
* Single default invoice template
* One reminder schedule per invoice (system-defined)
* Stripe as primary payment provider

---

## 10. Future Extensions

* Recurring invoices table
* Multi-currency support
* Client portal users
* Audit logs

---

**Status:** MVP Ready

# Emitfy – Product Requirements Document (PRD)

## 1. Product Overview

**Product Name:** Emitfy
**Product Type:** SaaS (Web Application)
**Target Users:** Solo freelancers (developers, designers, consultants, writers)

Emitfy is a lightweight invoicing SaaS designed for freelancers who currently rely on spreadsheets to track invoices and payments. The product focuses exclusively on invoicing, payment tracking, and reminders — avoiding full accounting complexity.

The core promise is: **“Create and send your first invoice in 60 seconds.”**

---

## 2. Problem Statement

Freelancers managing multiple clients face recurring issues when handling invoices manually:

* Manual spreadsheet updates consume 2–4 hours per week
* High rate of late payments due to lack of reminders
* Calculation errors and poor visibility of outstanding revenue
* Stress during tax season and financial planning

Existing tools are either:

* Too expensive ($15–$50/month)
* Overly complex (full accounting systems)
* Poorly optimized for solo freelancers

---

## 3. Goals & Success Metrics

### Primary Goals

* Reduce time spent managing invoices
* Increase on-time payments
* Provide clear visibility into outstanding revenue

---

## 4. Target Audience

**Primary Persona: Solo Freelancer**

* Manages 3–15 clients
* Monthly income: $2,000–$10,000
* Uses Google Sheets or Excel today
* Avoids expensive accounting software
* Values simplicity and speed over advanced features

---

## 5. Value Proposition

SimpleInvoice offers:

* Extremely fast onboarding
* Minimal, distraction-free UI
* Affordable pricing
* Automated reminders without accounting overhead

**Positioning:**

> A simple invoicing tool for freelancers who hate spreadsheets.

---

## 6. Core Features (MVP)

### 6.1 Authentication & Account Setup

* Email/password authentication
* Minimal profile setup (name, email)

### 6.2 Client Management

* Create, edit, delete clients
* Required fields: name, email
* Optional fields: company name, notes

### 6.3 Invoice Creation

* Simple invoice editor
* Invoice number auto-generated
* Issue date and due date
* Line items (description, quantity, unit price)
* Automatic subtotal and total calculation

### 6.4 Invoice Templates

* Single clean default template
* Brand color and logo support
* Optimized for PDF and email

### 6.5 Invoice Status Tracking

* Draft
* Sent
* Paid
* Overdue

Status updates automatically based on due date and payment events.

### 6.6 Dashboard

* Total outstanding amount
* Overdue invoices
* Upcoming due invoices (next 7 days)

### 6.7 Email Delivery

* Send invoice via email
* Public invoice link
* Payment call-to-action in email

### 6.8 Automated Reminders

* Reminder 3 days before due date
* Reminder on due date
* Reminder 7 days after due date

### 6.9 Payments

* Stripe payment link integration
* PayPal payment link (optional for MVP)
* Webhooks to update invoice status

### 6.10 Export

* Export invoices to PDF
* Export summary to CSV/Excel

---

## 7. Non-Goals (MVP Exclusions)

* Full accounting features
* Expense tracking
* Payroll
* Multi-user teams
* Tax calculation
* Mobile application

---

## 8. User Flow (Happy Path)

1. User signs up
2. Creates first client
3. Adds invoice item
4. Clicks “Send Invoice”
5. Client receives email with payment link
6. Invoice status updates upon payment

---

## 9. Technical Requirements

### 9.1 Stack

* Backend: Laravel
* Frontend: Vue.js with Inertia
* Database: SQLITE
* Payments: undefined for now
* Emails: laravel email

### 9.2 Performance

* Invoice creation < 500ms
* Dashboard load < 1s

### 9.3 Security

* HTTPS everywhere
* Auth-protected routes
* Secure webhook validation

---

## 10. Monetization

### Pricing Tiers

**Free**

* Up to 5 invoices/month

**Pro – $9/month**

* Unlimited invoices
* Automated reminders
* PDF & CSV export

**Business – $19/month**

* Multiple currencies
* Recurring invoices
* Basic reports

---

## 11. Risks & Mitigations

| Risk                           | Mitigation                       |
| ------------------------------ | -------------------------------- |
| Low differentiation            | Focus on onboarding speed and UX |
| Payment integration complexity | Start with Stripe only           |
| Feature creep                  | Strict MVP scope                 |

---

## 12. Future Enhancements

* Recurring invoices
* Multi-currency support
* Custom reminder schedules
* Client portal
* Mobile app

---

## 13. Launch Criteria

* First invoice can be sent end-to-end
* Payment successfully updates status
* Reminder emails are sent correctly
* PDF export works reliably

---

**Document Owner:** Product / Founder
**Status:** MVP Definition
**Last Updated:** Feb 2026

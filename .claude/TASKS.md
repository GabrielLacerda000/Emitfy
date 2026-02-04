# SimpleInvoice - Development Tasks & Milestones

## Project Overview
**Stack:** Laravel 12 + Vue 3 + Inertia.js + TypeScript + Tailwind CSS 4
**Database:** SQLite (UUIDs for primary keys)
**Auth:** Laravel Fortify (already implemented)

---

## Current Status

### Already Implemented (Laravel Vue Starter Kit)
- [x] User registration & login
- [x] Email verification
- [x] Password reset flow
- [x] Two-factor authentication (2FA)
- [x] User settings (profile, password, appearance)
- [x] Account deletion
- [x] UI component library (shadcn/ui style)
- [x] App layout with sidebar/header

---

## Milestone 1: Database Foundation

### 1.1 Update Users Table
- [x] Add migration for new user fields
  - `currency` (string) - Default currency e.g. BRL
  - `logo_url` (string, nullable) - Logo image path
- [x] Update `User` model with new fillable fields

### 1.2 Clients Table
- [x] Create `clients` migration
  - `id` (UUID, primary key)
  - `user_id` (UUID, FK → users.id)
  - `name` (string) - Client name
  - `email` (string) - Billing email
  - `company_name` (string, nullable)
  - `notes` (text, nullable)
  - `timestamps`
- [x] Create `Client` model
  - `belongsTo` User
  - `hasMany` Invoices
- [x] Add `hasMany` Clients relationship to User model

### 1.3 Invoices Table
- [x] Create `invoices` migration
  - `id` (UUID, primary key)
  - `user_id` (UUID, FK → users.id)
  - `client_id` (UUID, FK → clients.id)
  - `number` (string) - Auto-generated invoice number
  - `status` (enum: draft, sent, paid, overdue)
  - `issue_date` (date)
  - `due_date` (date)
  - `subtotal` (decimal)
  - `tax` (decimal, default 0)
  - `total` (decimal)
  - `notes` (text, nullable)
  - `public_token` (string) - For public invoice link
  - `sent_at` (timestamp, nullable)
  - `paid_at` (timestamp, nullable)
  - `timestamps`
- [x] Create `Invoice` model
  - `belongsTo` User
  - `belongsTo` Client
  - `hasMany` InvoiceItems
  - `hasMany` Payments
  - `hasMany` ReminderSchedules

### 1.4 Invoice Items Table
- [x] Create `invoice_items` migration
  - `id` (UUID, primary key)
  - `invoice_id` (UUID, FK → invoices.id)
  - `description` (string)
  - `quantity` (integer, default 1)
  - `unit_price` (decimal)
  - `total` (decimal) - quantity × unit_price
  - `timestamps`
- [x] Create `InvoiceItem` model
  - `belongsTo` Invoice

### 1.5 Payments Table
- [ ] Create `payments` migration
  - `id` (UUID, primary key)
  - `invoice_id` (UUID, FK → invoices.id)
  - `provider` (enum: stripe, paypal)
  - `provider_payment_id` (string) - External reference
  - `amount` (decimal)
  - `status` (enum: pending, completed, failed)
  - `paid_at` (timestamp, nullable)
  - `timestamps`
- [ ] Create `Payment` model
  - `belongsTo` Invoice

### 1.6 Reminder Schedules Table
- [ ] Create `reminder_schedules` migration
  - `id` (UUID, primary key)
  - `invoice_id` (UUID, FK → invoices.id)
  - `type` (enum: before_due, on_due, after_due)
  - `offset_days` (integer) - e.g. -3, 0, +7
  - `sent_at` (timestamp, nullable)
  - `timestamps`
- [ ] Create `ReminderSchedule` model
  - `belongsTo` Invoice

### 1.7 Subscriptions Table
- [ ] Create `subscriptions` migration
  - `id` (UUID, primary key)
  - `user_id` (UUID, FK → users.id)
  - `plan` (enum: free, pro, business)
  - `provider` (enum: stripe)
  - `provider_subscription_id` (string, nullable)
  - `status` (enum: active, canceled, past_due)
  - `current_period_end` (timestamp, nullable)
  - `timestamps`
- [ ] Create `Subscription` model
  - `belongsTo` User
- [ ] Add `hasOne` Subscription relationship to User model

---

## Milestone 2: Client Management Feature

### 2.1 Backend - Client CRUD
- [ ] Create `ClientController`
  - `index()` - List user's clients
  - `create()` - Show create form
  - `store()` - Save new client
  - `edit()` - Show edit form
  - `update()` - Update client
  - `destroy()` - Delete client
- [ ] Create `StoreClientRequest` form request
- [ ] Create `UpdateClientRequest` form request
- [ ] Register client routes in `routes/web.php`

### 2.2 Frontend - Client Pages
- [ ] Create `resources/js/pages/clients/Index.vue`
  - Clients table with name, email, company, invoice count
  - Search/filter functionality
  - Create new client button
- [ ] Create `resources/js/pages/clients/Create.vue`
  - Form: name, email, company_name, notes
- [ ] Create `resources/js/pages/clients/Edit.vue`
  - Pre-filled form with existing data
- [ ] Add "Clients" link to sidebar navigation (`AppSidebar.vue`)

### 2.3 Client UI Components
- [ ] Client delete confirmation modal
- [ ] Empty state for no clients
- [ ] Client card/row component

---

## Milestone 3: Invoice Creation Feature

### 3.1 Backend - Invoice CRUD
- [ ] Create `InvoiceController`
  - `index()` - List user's invoices with filters
  - `create()` - Show create form
  - `store()` - Save new invoice with items
  - `show()` - View single invoice
  - `edit()` - Show edit form
  - `update()` - Update invoice and items
  - `destroy()` - Delete invoice
- [ ] Create `StoreInvoiceRequest` form request
- [ ] Create `UpdateInvoiceRequest` form request
- [ ] Create `InvoiceService` for business logic
  - Auto-generate invoice number (format: INV-YYYYMM-001)
  - Generate unique `public_token`
  - Calculate subtotal/total
- [ ] Register invoice routes

### 3.2 Frontend - Invoice List
- [ ] Create `resources/js/pages/invoices/Index.vue`
  - Invoice table with number, client, amount, status, due date
  - Status filter tabs (All, Draft, Sent, Paid, Overdue)
  - Sort by date, amount, client
  - Status badges with colors
  - Create new invoice button

### 3.3 Frontend - Invoice Editor
- [ ] Create `resources/js/pages/invoices/Create.vue`
- [ ] Create `resources/js/pages/invoices/Edit.vue`
- [ ] Create `resources/js/pages/invoices/Show.vue` (view only)
- [ ] Invoice form components:
  - [ ] Client selector dropdown (with option to create new)
  - [ ] Date pickers (issue date, due date)
  - [ ] Line items table component
    - Add/remove rows
    - Description, quantity, unit price inputs
    - Auto-calculate row total
  - [ ] Subtotal, tax, total display
  - [ ] Notes textarea
  - [ ] Save as Draft / Save & Send buttons

### 3.4 Invoice Actions
- [ ] Duplicate invoice action
- [ ] Delete invoice with confirmation modal
- [ ] Add "Invoices" link to sidebar navigation

---

## Milestone 4: Invoice Status Tracking

### 4.1 Status Management Backend
- [ ] Create `InvoiceStatus` enum
  - `DRAFT`, `SENT`, `PAID`, `OVERDUE`
- [ ] Add status methods to Invoice model
  - `markAsSent()` - Set status, sent_at timestamp
  - `markAsPaid()` - Set status, paid_at timestamp
  - `markAsOverdue()` - Set status
  - `isDraft()`, `isSent()`, `isPaid()`, `isOverdue()` helpers
- [ ] Create scheduled command `MarkOverdueInvoices`
  - Run daily, find sent invoices past due_date
  - Update status to overdue

### 4.2 Status UI
- [ ] Status badge component with colors
  - Draft: gray
  - Sent: blue
  - Paid: green
  - Overdue: red
- [ ] Status change buttons on invoice view
- [ ] Manual "Mark as Paid" action

---

## Milestone 5: Dashboard Feature

### 5.1 Dashboard Backend
- [ ] Create `DashboardController`
- [ ] Calculate stats:
  - Total outstanding (sent + overdue invoices)
  - Total overdue amount
  - Count of overdue invoices
  - Invoices due in next 7 days
- [ ] Get recent invoices (last 5)
- [ ] Get recent clients (last 5)

### 5.2 Dashboard UI
- [ ] Update `resources/js/pages/Dashboard.vue`
  - [ ] Stats cards row
    - Outstanding amount
    - Overdue amount
    - Invoices due soon
  - [ ] Quick actions
    - New Invoice button
    - New Client button
  - [ ] Recent invoices table
  - [ ] Empty state for new users

---

## Milestone 6: User Business Settings

### 6.1 Business Settings Backend
- [ ] Create `BusinessSettingsController`
  - `edit()` - Show settings form
  - `update()` - Save settings
- [ ] Create `BusinessSettingsRequest` for validation
- [ ] Add route `/settings/business`

### 6.2 Business Settings UI
- [ ] Create `resources/js/pages/settings/Business.vue`
  - Currency selector dropdown
  - Logo upload with preview
  - Logo delete functionality
- [ ] Add "Business" link to settings sidebar

### 6.3 Logo Storage
- [ ] Configure public disk for logo storage
- [ ] Create logo upload endpoint
- [ ] Generate logo URL accessor in User model

---

## Milestone 7: Invoice Templates & PDF

### 7.1 Invoice PDF Template
- [ ] Install DomPDF package (`barryvdh/laravel-dompdf`)
- [ ] Create Blade template `resources/views/pdf/invoice.blade.php`
  - User logo
  - Invoice number, dates
  - Client details
  - Line items table
  - Subtotal, tax, total
  - Payment instructions/link

### 7.2 PDF Generation
- [ ] Create `InvoicePdfController`
  - `download()` - Generate and download PDF
  - `stream()` - Stream PDF in browser
- [ ] Add download PDF button to invoice view
- [ ] Add route `/invoices/{invoice}/pdf`

---

## Milestone 8: Email Delivery

### 8.1 Invoice Email
- [ ] Create `InvoiceSentMail` Mailable class
- [ ] Create email template `resources/views/mail/invoice-sent.blade.php`
  - Invoice summary
  - View invoice button (public link)
  - Payment instructions
- [ ] Attach PDF to email (optional)

### 8.2 Send Invoice Action
- [ ] Add `send()` method to `InvoiceController`
- [ ] Create route `POST /invoices/{invoice}/send`
- [ ] Send email to client
- [ ] Update invoice status to `sent`
- [ ] Set `sent_at` timestamp

### 8.3 Public Invoice View
- [ ] Create `PublicInvoiceController`
  - `show()` - Display invoice via public_token
- [ ] Create route `GET /i/{public_token}` (short public URL)
- [ ] Create `resources/js/pages/invoices/Public.vue`
  - Invoice details (no auth required)
  - Pay now button

---

## Milestone 9: Automated Reminders

### 9.1 Reminder Email
- [ ] Create `InvoiceReminderMail` Mailable class
- [ ] Create email template `resources/views/mail/invoice-reminder.blade.php`
  - Different copy for before/on/after due date

### 9.2 Reminder Scheduling
- [ ] Create `CreateReminderSchedules` service
  - Create 3 reminders per invoice: -3, 0, +7 days
- [ ] Auto-create reminders when invoice is sent
- [ ] Delete reminders when invoice is paid

### 9.3 Reminder Command
- [ ] Create `SendInvoiceReminders` Artisan command
- [ ] Query reminder_schedules where:
  - `sent_at` is null
  - Scheduled date matches today
- [ ] Send reminder email
- [ ] Update `sent_at` timestamp
- [ ] Schedule command to run daily

---

## Milestone 10: Payments Integration

### 10.1 Stripe Setup
- [ ] Install Stripe PHP SDK
- [ ] Add Stripe config (publishable key, secret key)
- [ ] Create environment variables

### 10.2 Payment Link Generation
- [ ] Create `PaymentController`
  - `createCheckoutSession()` - Generate Stripe checkout
- [ ] Add Stripe checkout button on public invoice
- [ ] Pass invoice details to Stripe

### 10.3 Webhook Handling
- [ ] Create `StripeWebhookController`
  - `handle()` - Process Stripe webhooks
- [ ] Verify webhook signature
- [ ] Handle `checkout.session.completed` event:
  - Find invoice by metadata
  - Create Payment record
  - Update invoice status to `paid`
  - Set `paid_at` timestamp
- [ ] Add webhook route `/stripe/webhook`

---

## Milestone 11: Export Feature

### 11.1 Single Invoice PDF
- [ ] PDF download from invoice view (from M7)

### 11.2 Invoice List Export
- [ ] Create `ExportController`
  - `invoicesCsv()` - Export to CSV
- [ ] Add export button to invoice list page
- [ ] Export columns: number, client, issue_date, due_date, status, total
- [ ] Support date range filter
- [ ] Support status filter
- [ ] Add route `GET /invoices/export`

---

## Milestone 12: Subscriptions & Limits

### 12.1 Plan Enforcement
- [ ] Create `CheckInvoiceLimit` middleware
- [ ] Free tier: 5 invoices per month
- [ ] Query monthly invoice count
- [ ] Block invoice creation if limit reached

### 12.2 Subscription Integration (Post-MVP)
- [ ] Create pricing page
- [ ] Stripe subscription checkout
- [ ] Webhook for subscription events
- [ ] Plan upgrade/downgrade logic

---

## Priority Order for MVP

| Order | Milestone | Description | Critical Path |
|-------|-----------|-------------|---------------|
| 1 | M1 | Database Foundation | Yes |
| 2 | M2 | Client Management | Yes |
| 3 | M3 | Invoice Creation | Yes |
| 4 | M4 | Status Tracking | Yes |
| 5 | M5 | Dashboard | Yes |
| 6 | M6 | Business Settings | Yes |
| 7 | M7 | PDF Generation | Yes |
| 8 | M8 | Email Delivery | Yes |
| 9 | M9 | Automated Reminders | No |
| 10 | M10 | Payments | No |
| 11 | M11 | Export | No |
| 12 | M12 | Subscriptions | No |

---

## Entity Relationships Reference

```
User
├── hasMany → Clients
├── hasMany → Invoices
└── hasOne  → Subscription

Client
├── belongsTo → User
└── hasMany   → Invoices

Invoice
├── belongsTo → User
├── belongsTo → Client
├── hasMany   → InvoiceItems
├── hasMany   → Payments
└── hasMany   → ReminderSchedules

InvoiceItem
└── belongsTo → Invoice

Payment
└── belongsTo → Invoice

ReminderSchedule
└── belongsTo → Invoice

Subscription
└── belongsTo → User
```

---

## Key Files Reference

### Existing (Reuse)
- `app/Models/User.php` - Add relationships, new fields
- `resources/js/layouts/AppLayout.vue` - Main app layout
- `resources/js/components/ui/*` - Full UI component library
- `resources/js/components/AppSidebar.vue` - Add navigation items

### Models to Create
- `app/Models/Client.php`
- `app/Models/Invoice.php`
- `app/Models/InvoiceItem.php`
- `app/Models/Payment.php`
- `app/Models/ReminderSchedule.php`
- `app/Models/Subscription.php`

### Controllers to Create
- `app/Http/Controllers/ClientController.php`
- `app/Http/Controllers/InvoiceController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/PublicInvoiceController.php`
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Controllers/StripeWebhookController.php`
- `app/Http/Controllers/ExportController.php`
- `app/Http/Controllers/Settings/BusinessSettingsController.php`

### Vue Pages to Create
- `resources/js/pages/clients/Index.vue`
- `resources/js/pages/clients/Create.vue`
- `resources/js/pages/clients/Edit.vue`
- `resources/js/pages/invoices/Index.vue`
- `resources/js/pages/invoices/Create.vue`
- `resources/js/pages/invoices/Edit.vue`
- `resources/js/pages/invoices/Show.vue`
- `resources/js/pages/invoices/Public.vue`
- `resources/js/pages/settings/Business.vue`

---

## Verification Plan

After each milestone:
1. Run `php artisan test` - All tests pass
2. Run `composer lint` - No PHP style issues
3. Run `npm run lint` - No JS/Vue issues
4. Manual browser test - Feature works end-to-end
5. Database check via `mcp__plugin_laravel-boost_laravel-boost__tinker` - Data persists correctly

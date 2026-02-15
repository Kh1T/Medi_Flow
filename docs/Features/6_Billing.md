# 💰 Phase 6 — Billing & Insurance

> **Prerequisites:** Phase 1, Phase 3
> **Dependencies:** Patients, Appointments
> **Status:** ⬜ Not Started

---

## 🎯 Goal

Generate invoices after appointments, calculate insurance coverage, and record payments.

---

## 📦 Database Fields

### `insurances` table

| Field                  | Type     | Notes                        |
|------------------------|----------|------------------------------|
| `patient_id`           | FK       | Links to `patients`          |
| `provider_name`        | string   | Insurance company name       |
| `policy_number`        | string   | Policy ID                    |
| `coverage_percentage`  | decimal  | e.g. 80.00 = 80%            |
| `valid_until`          | date     | Expiry date                  |

### `invoices` table

| Field                | Type     | Notes                              |
|----------------------|----------|------------------------------------|
| `patient_id`         | FK       | Links to `patients`                |
| `appointment_id`     | FK       | Links to `appointments`            |
| `invoice_number`     | string   | Auto-generated unique number       |
| `subtotal`           | decimal  |                                    |
| `tax`                | decimal  |                                    |
| `total`              | decimal  | subtotal + tax                     |
| `insurance_claim`    | boolean  | Was insurance applied?             |
| `insurance_company`  | string   | Nullable                           |
| `insurance_coverage` | decimal  | Amount covered by insurance        |
| `patient_amount`     | decimal  | Amount patient owes                |
| `paid_amount`        | decimal  | Amount actually paid               |
| `status`             | enum     | `pending`, `paid`, `overdue`       |
| `due_date`           | date     |                                    |

---

## 🔧 Steps

### Step 6.1 — Insurance Model & Migration

```bash
php artisan make:model Insurance -mcr
```

---

### Step 6.2 — Invoice Model & Migration

```bash
php artisan make:model Invoice -mcr
```

---

### Step 6.3 — BillingService

```bash
php artisan make:service BillingService
```

Key methods:

| Method                                  | Purpose                                |
|-----------------------------------------|----------------------------------------|
| `calculateInsuranceCoverage($patientId, $total)` | Return covered & patient amounts |
| `generateInvoice($patientId, $appointmentId, $fee, $extras)` | Create invoice record |

---

### Step 6.4 — Invoice Generation

Two triggers:

| Trigger     | How                                               |
|-------------|----------------------------------------------------|
| Automatic   | Event/Listener fires when appointment → `completed` |
| Manual      | Receptionist creates invoice from billing panel     |

---

### Step 6.5 — Payment Recording

- Form to record payment amount and method.
- Update invoice `paid_amount` and `status`.

---

### Step 6.6 — Invoice PDF *(Optional)*

- Use [DomPDF](https://github.com/barryvdh/laravel-dompdf) to export invoices as PDF.

```bash
composer require barryvdh/laravel-dompdf
```

---

## ✅ Done When

- [ ] Insurance info can be stored per patient
- [ ] Invoices are generated (auto or manual)
- [ ] Insurance coverage is calculated correctly
- [ ] Payments can be recorded
- [ ] Invoice status updates (`pending` → `paid`)
- [ ] *(Optional)* PDF export works

# 📅 Phase 3 — Appointment Scheduling

> **Prerequisites:** Phase 1, Phase 2
> **Dependencies:** Patients, Doctors, Availabilities
> **Status:** ⬜ Not Started

---

## 🎯 Goal

Book, view, and manage patient appointments with calendar UI and conflict detection.

---

## 📦 Database Fields

### `appointments` table

| Field              | Type      | Notes                                  |
|--------------------|-----------|----------------------------------------|
| `patient_id`       | FK        | Links to `patients`                    |
| `doctor_id`        | FK        | Links to `doctors`                     |
| `appointment_date` | date      |                                        |
| `appointment_time` | time      |                                        |
| `status`           | enum      | See status list below                  |
| `symptoms`         | text      | Patient-reported symptoms              |
| `notes`            | text      | Nullable                               |
| `created_by`       | FK        | User who booked (receptionist/patient) |

### Appointment Statuses

| Status       | Meaning                        |
|--------------|--------------------------------|
| `pending`    | Awaiting confirmation          |
| `confirmed`  | Doctor confirmed               |
| `checked-in` | Patient arrived                |
| `completed`  | Visit finished                 |
| `cancelled`  | Cancelled by patient or staff  |
| `no-show`    | Patient did not attend         |

---

## 🔧 Steps

### Step 3.1 — Install FullCalendar

```bash
npm install fullcalendar
```

---

### Step 3.2 — Appointment Model & Migration

```bash
php artisan make:model Appointment -mcr
```

- Define relationships: `belongsTo(Patient)`, `belongsTo(Doctor)`.

---

### Step 3.3 — AppointmentService

```bash
php artisan make:service AppointmentService
```

Key methods:

| Method               | Purpose                              |
|----------------------|--------------------------------------|
| `isSlotAvailable()`  | Check if a time slot is free         |
| `getAvailableSlots()`| Return open slots for a given doctor |

---

### Step 3.4 — Calendar UI (Livewire)

- **Livewire component** `AppointmentCalendar` renders FullCalendar.
- **Booking modal:** select patient → doctor → date/time → symptoms.
- **Conflict detection** via `AppointmentService` before saving.

---

### Step 3.5 — Status Management

- Allow staff to update appointment status.
- *(Optional)* Add audit logging with [Spatie Activitylog](https://spatie.be/docs/laravel-activitylog).

---

## ✅ Done When

- [ ] Calendar displays appointments visually
- [ ] New appointments can be booked via modal
- [ ] Double-booking is prevented (conflict detection)
- [ ] Appointment status can be updated
- [ ] Available slots are calculated from doctor availability


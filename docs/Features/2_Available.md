# 👨‍⚕️ Phase 2 — Doctors & Availability

> **Prerequisites:** Phase 0, Phase 1
> **Dependencies:** Users (doctors are users with profiles)
> **Status:** ⬜ Not Started

---

## 🎯 Goal

Manage doctor profiles and their weekly/specific-date availability slots.

---

## 📦 Database Fields

### `doctors` table

| Field               | Type     | Notes                        |
|---------------------|----------|------------------------------|
| `user_id`           | FK       | Links to `users` table       |
| `specialization`    | string   | e.g. Cardiology, Pediatrics  |
| `qualification`     | string   | e.g. MBBS, MD                |
| `license_number`    | string   | Medical license ID           |
| `experience_years`  | integer  |                              |
| `consultation_fee`  | decimal  | Fee per visit                |
| `is_available`      | boolean  | Quick toggle                 |

### `availabilities` table

| Field            | Type     | Notes                          |
|------------------|----------|--------------------------------|
| `doctor_id`      | FK       | Links to `doctors` table       |
| `day_of_week`    | tinyint  | 0 (Sun) – 6 (Sat)             |
| `start_time`     | time     |                                |
| `end_time`       | time     |                                |
| `is_recurring`   | boolean  | Weekly repeat?                 |
| `specific_date`  | date     | Nullable — for one-off slots   |

### Relationships

```
Doctor  belongsTo  User
Doctor  hasMany    Appointment
Doctor  hasMany    Availability
```

---

## 🔧 Steps

### Step 2.1 — Doctor Model & Migration

```bash
php artisan make:model Doctor -mcr
```

- Define relationships: `belongsTo(User)`, `hasMany(Appointment)`, `hasMany(Availability)`.

---

### Step 2.2 — Availability Model & Migration

```bash
php artisan make:model Availability -mcr
```

---

### Step 2.3 — Doctor Seeder

- Create a user with the `Doctor` role.
- Attach a doctor profile with availability slots.

---

### Step 2.4 — Availability UI

- Form to set **recurring weekly slots** or **specific dates**.
- Display the doctor's current schedule in a readable format.

---

### Step 2.5 — Doctor Listing

- Public-facing list of doctors with specialization, fee, and availability.
- Used by the appointment booking flow (Phase 3).

---

## ✅ Done When

- [ ] Doctor profiles are linked to user accounts
- [ ] Availability can be set (recurring + one-off)
- [ ] Doctor listing page shows specialization & fee
- [ ] Seeder creates test doctor with availability

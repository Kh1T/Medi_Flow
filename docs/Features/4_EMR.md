# 📋 Phase 4 — Electronic Medical Records (EMR)

> **Prerequisites:** Phase 1, Phase 2, Phase 3
> **Dependencies:** Patients, Doctors, Appointments (optional link)
> **Status:** ⬜ Not Started

---

## 🎯 Goal

Allow doctors to create versioned medical records with file attachments.

---

## 📦 Database Fields

### `medical_records` table

| Field                 | Type     | Notes                             |
|-----------------------|----------|-----------------------------------|
| `patient_id`          | FK       | Links to `patients`               |
| `doctor_id`           | FK       | Links to `doctors`                |
| `visit_date`          | date     |                                   |
| `diagnosis`           | text     |                                   |
| `symptoms`            | text     | Recorded by doctor                |
| `treatment`           | text     | Treatment plan                    |
| `notes`               | text     | Nullable                          |
| `version`             | integer  | Auto-incremented per patient      |
| `previous_version_id` | FK       | Nullable — links to previous rev  |

### Attachments

Uses **Spatie Media Library** (`InteractsWithMedia` trait) for:
- Lab results (PDF, images)
- X-rays / scans
- Prescriptions

---

## 🔧 Steps

### Step 4.1 — Verify Media Library

Ensure [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) is installed and configured.

---

### Step 4.2 — MedicalRecord Model & Migration

```bash
php artisan make:model MedicalRecord -mcr
```

- Add `InteractsWithMedia` trait.
- Define relationships: `belongsTo(Patient)`, `belongsTo(Doctor)`.

---

### Step 4.3 — Versioning Logic

- Implement `saveAsNewVersion()` method in the model.
- Each edit creates a **new version**, preserving the previous one.

```
v1 ← v2 ← v3 (current)
```

---

### Step 4.4 — Controller & Views

- **Create record:** Doctor selects patient (or pre-fill from appointment).
- **View record:** Show diagnosis, treatment, notes, attachments.
- **Version history:** List all versions with diff view.
- **Upload attachments:** Drag-and-drop file upload.

---

## ✅ Done When

- [ ] Doctors can create medical records for patients
- [ ] Records can be created from an appointment (pre-filled)
- [ ] Version history is visible and browsable
- [ ] Files can be uploaded and viewed as attachments
- [ ] Previous versions are preserved (not overwritten)

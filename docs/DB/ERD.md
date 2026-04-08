# Medi_Flow Entity Relationship Diagram

## Complete Database Schema ERD

```
╔══════════════════════════════════════════════════════════════════════════════════════════════╗
║                                        CORE ENTITIES                                          ║
╠══════════════════════════╗       ╔═══════════════════════════╗       ╔══════════════════════╣
║         USERS            ║──────▶│         DOCTORS            │◀──────│    AVAILABILITIES    ║
║  ────────────────────    ║  1:N  ║  ──────────────────────    ║  1:N  ║  ─────────────────    ║
║  • id (PK)               ║       ║  • id (PK)                 ║       ║  • id (PK)           ║
║  • name                  ║       ║  • user_id (FK)           ║       ║  • doctor_id (FK)    ║
║  • email                 ║       ║  • specialization         ║       ║  • day_of_week       ║
║  • role                  ║       ║  • qualification          ║       ║  • start_time        ║
║  • phone                 ║       ║  • license_number          ║       ║  • end_time          ║
╚══════════════════════════╝       ╚═══════════════════════════╝       ╚══════════════════════╝
         │                                  │
         │ 1:1                              │ 1:N
         ▼                                  ▼
╔══════════════════════════╗     ╔═══════════════════════════╗     ╔══════════════════════╗
║        PATIENTS          │◀────│        OPD_VISITS          │     ║                        ║
║  ────────────────────    ║ 1:N ║  ──────────────────────    ║     ║                        ║
║  • id (PK)               │     ║  • id (PK)                 ║     ║                        ║
║  • user_id (FK, nullable)║     ║  • patient_id (FK)        ║     ║                        ║
║  • first_name            │     ║  • doctor_id (FK)        ║     ║                        ║
║  • last_name             │     ║  • token_number           ║     ║                        ║
║  • dob                   │     ║  • visit_date             ║     ║                        ║
║  • gender                │     ║  • symptoms               ║     ║                        ║
║  • blood_group           │     ║  • diagnosis              ║     ║                        ║
║  • phone                 │     ║  • visit_type             ║     ║                        ║
║  • email                 │     ║  • fee                    ║     ║                        ║
║  • address               │     ║  • payment_status        ║     ║                        ║
║  • emergency_contact     │     ║  • status                 ║     ║                        ║
╚══════════════════════════╝     ╚═══════════════════════════╝     ║                        ║
         │                              │                           ║                        ║
         │ 1:N                           │ 1:N                       ║                        ║
         ▼                              ▼                           ╚══════════════════════╝
╔══════════════════════════╗     ╔═══════════════════════════╗
║     IPD_ADMISSIONS       │◀────│     MEDICAL_RECORDS         │
║  ────────────────────    ║ 1:N ║  ──────────────────────    │
║  • id (PK)               │     ║  • id (PK)                 │
║  • patient_id (FK)       │     ║  • patient_id (FK)        │
║  • doctor_id (FK)        │     ║  • doctor_id (FK)        │
║  • bed_id (FK, nullable) │     ║  • visit_date             │
║  • admission_date        │     ║  • diagnosis              │
║  • discharge_date        │     ║  • symptoms              │
║  • ward_type             │     ║  • treatment              │
║  • bed_number            │     ║  • version               │
║  • admission_type        │     ║  • previous_version_id   │
║  • diagnosis             │     ╚═══════════════════════════╝
║  • status                │              │ 1:N
╚══════════════════════════╝              ▼
         │
         │ 1:N              ┌─────────────────────────────────────────────────────────────┐
         ├──────────────────┤                         BILLING (INVOICES)               │
         │                  ├─────────────────────────────────────────────────────────────┤
         │                  │  • id (PK)                                                  │
         │                  │  • patient_id (FK)                                         │
         ▼                  │  • opd_visit_id (FK, nullable)                             │
╔══════════════════════╗   │  • ipd_admission_id (FK, nullable)                        │
║     IPD_NOTES         │   │  • invoice_number (unique)                                │
║  ─────────────────    │   │  • consultation_fee, lab_charges, medicine_charges...    │
║  • id (PK)            │   │  • subtotal, tax, total                                  │
║  • ipd_admission_id   │   │  • insurance_claim, insurance_coverage                    │
║  • author_id (FK)     │   │  • patient_amount, paid_amount                           │
║  • note_type          │   │  • status, payment_method                                │
║  • notes              │   │  • bed_type, bed_days, bed_charges                        │
╚══════════════════════╝   └─────────────────────────────────────────────────────────────┘
         │
╔══════════════════════╗
║  IPD_MEDICATIONS      │
║  ─────────────────    │
║  • id (PK)            │
║  • ipd_admission_id   │
║  • medicine_name      │
║  • dosage             │
║  • administered_at    │
║  • administered_by    │
╚══════════════════════╝

╔══════════════════════╗
║  IPD_LAB_REQUESTS    │
║  ─────────────────    │
║  • id (PK)            │
║  • ipd_admission_id   │
║  • test_name          │
║  • status             │
║  • result_notes       │
╚══════════════════════╝

╔════════════════════════════════════════════════════════════════════════════════════════════╗
║                                      PRESCRIPTION FLOW                                      ║
╠════════════════════════════════════════════════════════════════════════════════════════════╣
║                                                                                              ║
║    MEDICAL_RECORD (1:N)    OPD_VISIT (1:N)    IPD_ADMISSION (1:N)                           ║
║           │                      │                    │                                    ║
║           │                      │                    │                                    ║
║           └──────────────────────┼────────────────────┘                                    ║
║                                  ▼                                                          ║
║                    ╔════════════════════════════════════════════╗                          ║
║                    ║            PRESCRIPTIONS                    ║                          ║
║                    ║  ────────────────────────────────────────   ║                          ║
║                    ║  • id (PK)                                  ║                          ║
║                    ║  • medical_record_id (FK, nullable)         ║                          ║
║                    ║  • opd_visit_id (FK, nullable)             ║                          ║
║                    ║  • ipd_admission_id (FK, nullable)         ║                          ║
║                    ║  • doctor_id (FK)                           ║                          ║
║                    ║  • patient_id (FK)                         ║                          ║
║                    ║  • diagnosis                                ║                          ║
║                    ║  • status (active/dispensed/expired)       ║                          ║
║                    ╚════════════════════════════════════════════╝                          ║
║                                  │ 1:N                                                      ║
║                                  ▼                                                          ║
║                    ╔════════════════════════════════════════════╗                          ║
║                    ║         PRESCRIPTION_ITEMS                   ║                          ║
║                    ║  ────────────────────────────────────────   ║                          ║
║                    ║  • id (PK)                                  ║                          ║
║                    ║  • prescription_id (FK)                     ║                          ║
║                    ║  • medicine_name                            ║                          ║
║                    ║  • dosage                                   ║                          ║
║                    ║  • frequency                                ║                          ║
║                    ║  • duration                                 ║                          ║
║                    ║  • instructions                             ║                          ║
║                    ║  • price                                    ║                          ║
║                    ║  • quantity                                 ║                          ║
║                    ╚════════════════════════════════════════════╝                          ║
║                                                                                              ║
╚════════════════════════════════════════════════════════════════════════════════════════════╝

╔════════════════════════════════════════════════════════════════════════════════════════════╗
║                                      INSURANCE FLOW                                         ║
╠════════════════════════════════════════════════════════════════════════════════════════════╣
║                                                                                              ║
║    PATIENT (1:N)          ───────────────▶        BILLING (INVOICES)                         ║
║           │                                     • insurance_claim                           ║
║           │                                     • insurance_company                         ║
║           │ 1:N                                 • insurance_coverage                         ║
║           ▼                                     • patient_amount                            ║
║    ╔════════════════════╗                                                                  ║
║    ║    INSURANCES      ║                                                                  ║
║    ║  ─────────────────    ║                                                                 ║
║    ║  • id (PK)          ║                                                                  ║
║    ║  • patient_id (FK)  ║                                                                 ║
║    ║  • provider_name    ║                                                                 ║
║    ║  • policy_number    ║                                                                 ║
║    ║  • coverage_%       ║                                                                 ║
║    ║  • valid_until      ║                                                                 ║
║    ╚════════════════════╝                                                                  ║
║                                                                                              ║
╚════════════════════════════════════════════════════════════════════════════════════════════╝
```

---

## Relationship Types

### One-to-One (1:1)
| Parent | Child | Description |
|--------|-------|-------------|
| User | Doctor | Each doctor has exactly one user account |
| Patient | User | Patient optionally has one user account (for registered patients) |
| OpdVisit | Billing | Each OPD visit has exactly one billing record |

### One-to-Many (1:N)
| Parent | Children | Description |
|--------|----------|-------------|
| Doctor | Availabilities | A doctor has multiple availability slots |
| Doctor | OpdVisits | A doctor can see many OPD patients |
| Doctor | IpdAdmissions | A doctor can admit many IPD patients |
| Doctor | MedicalRecords | A doctor creates many medical records |
| Patient | OpdVisits | A patient can have many OPD visits |
| Patient | IpdAdmissions | A patient can be admitted multiple times |
| Patient | Insurances | A patient can have multiple insurance policies |
| Patient | Prescriptions | A patient receives many prescriptions |
| OpdVisit | Prescriptions | An OPD visit can have prescriptions |
| IpdAdmission | IpdNotes | Multiple notes can be added during IPD stay |
| IpdAdmission | IpdMedications | Multiple medications can be administered |
| IpdAdmission | IpdLabRequests | Multiple lab tests can be requested |
| IpdAdmission | Prescriptions | IPD stay can have prescriptions |
| Prescription | PrescriptionItems | A prescription contains multiple items |
| Bed | IpdAdmissions | A bed can have multiple admissions (historical) |
| Billing | Patient | Many bills belong to one patient |

### Many-to-One (N:1)
All foreign key relationships are many-to-one from the child's perspective.

---

## Key Database Flow Examples

### OPD Patient Flow
```
User (Doctor) ──1:1──▶ Doctor ──1:N──▶ OpdVisit ──1:1──▶ Billing
                      │                     │
                      │                     ▼
                      │              MedicalRecord
                      │                     │
                      │                     ▼
                      │              Prescription ──1:N──▶ PrescriptionItems
                      │
◀────── Availabilities
```

### IPD Patient Flow
```
Patient ──1:N──▶ IpdAdmission ──1:N──▶ IpdNotes
                       │
                       ├──1:N──▶ IpdMedications
                       │
                       ├──1:N──▶ IpdLabRequests
                       │
                       ├──1:1──▶ Bed
                       │
                       └──1:1──▶ Billing
```

### Insurance Billing Flow
```
Patient ──1:N──▶ Insurance
     │
     └──1:N──▶ Billing (Invoices) ◀── insurance_claim, insurance_coverage
```

---

## Foreign Key References

| Column | References Table | On Delete |
|--------|------------------|-----------|
| patients.user_id | users | SET NULL |
| doctors.user_id | users | CASCADE |
| availabilities.doctor_id | doctors | CASCADE |
| medical_records.patient_id | patients | CASCADE |
| medical_records.doctor_id | doctors | CASCADE |
| opd_visits.patient_id | patients | CASCADE |
| opd_visits.doctor_id | doctors | CASCADE |
| ipd_admissions.patient_id | patients | CASCADE |
| ipd_admissions.doctor_id | doctors | CASCADE |
| ipd_admissions.bed_id | beds | SET NULL |
| prescriptions.medical_record_id | medical_records | CASCADE |
| prescriptions.doctor_id | doctors | CASCADE |
| prescriptions.patient_id | patients | CASCADE |
| prescriptions.opd_visit_id | opd_visits | SET NULL |
| prescriptions.ipd_admission_id | ipd_admissions | SET NULL |
| prescription_items.prescription_id | prescriptions | CASCADE |
| invoices.patient_id | patients | CASCADE |
| invoices.opd_visit_id | opd_visits | SET NULL |
| invoices.ipd_admission_id | ipd_admissions | SET NULL |
| insurances.patient_id | patients | CASCADE |
| ipd_notes.ipd_admission_id | ipd_admissions | CASCADE |
| ipd_notes.author_id | users | CASCADE |
| ipd_medications.ipd_admission_id | ipd_admissions | CASCADE |
| ipd_medications.administered_by | users | CASCADE |
| ipd_lab_requests.ipd_admission_id | ipd_admissions | CASCADE |
| notifications.user_id | users | CASCADE |

---

## Soft Deletes
Tables using Laravel SoftDeletes trait:
- `patients`
- `doctors`
- `opd_visits`
- `ipd_admissions`

These records can be restored after deletion.

# Medi_Flow Database Schema

## Overview
This document provides a comprehensive overview of the Medi_Flow hospital management system database schema. All tables are in a Laravel/MySQL environment.

---

## Core Tables

### 1. users
Authentication and user management table.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | VARCHAR(255) | NOT NULL | User's full name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User's email address |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| role | ENUM | DEFAULT 'patient' | One of: admin, doctor, receptionist, patient |
| phone | VARCHAR(255) | NULLABLE | Contact phone number |
| profile_photo | VARCHAR(255) | NULLABLE | Path to profile photo |
| remember_token | VARCHAR(100) | NULLABLE | Session remember token |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Related Tables:** doctors, patients, notifications, ipd_notes, ipd_medications

---

### 2. patients
Patient demographic and contact information.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to users table (nullable for walk-in patients) |
| first_name | VARCHAR(255) | NOT NULL | Patient's first name |
| last_name | VARCHAR(255) | NOT NULL | Patient's last name |
| dob | DATE | NOT NULL | Date of birth |
| gender | ENUM | NOT NULL | Male, Female, Other |
| blood_group | VARCHAR(255) | NULLABLE | Blood type (e.g., A+, B-, O+) |
| phone | VARCHAR(255) | NOT NULL | Primary contact number |
| email | VARCHAR(255) | NULLABLE | Patient's email address |
| address | TEXT | NULLABLE | Full address |
| emergency_contact | VARCHAR(255) | NULLABLE | Emergency contact name |
| emergency_phone | VARCHAR(255) | NULLABLE | Emergency contact phone |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Relationships:**
- `belongsTo` User (optional)
- `hasMany` OpdVisit
- `hasMany` IpdAdmission
- `hasMany` Insurance

---

### 3. doctors
Doctor professional information and specialization.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to users table |
| specialization | VARCHAR(255) | NOT NULL | Medical specialization |
| qualification | VARCHAR(255) | NULLABLE | Educational qualifications |
| license_number | VARCHAR(255) | UNIQUE, NOT NULL | Medical license number |
| experience_years | INT UNSIGNED | DEFAULT 0 | Years of experience |
| consultation_fee | DECIMAL(10,2) | DEFAULT 0 | Standard consultation fee |
| is_available | BOOLEAN | DEFAULT true | Availability status |
| available_days | JSON | NULLABLE | Array of available days |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Relationships:**
- `belongsTo` User
- `hasMany` Availability
- `hasMany` OpdVisit
- `hasMany` IpdAdmission

---

### 4. availabilities
Doctor weekly schedule and availability slots.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to doctors table |
| day_of_week | TINYINT | NOT NULL | 0=Sunday to 6=Saturday |
| start_time | TIME | NOT NULL | Slot start time |
| end_time | TIME | NOT NULL | Slot end time |
| is_recurring | BOOLEAN | DEFAULT true | Whether this is a recurring schedule |
| specific_date | DATE | NULLABLE | For one-off appointments |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` Doctor

---

## Clinical Tables

### 5. medical_records
Electronic Medical Records (EMR) containing patient visit information.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to patients table |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to doctors table |
| appointment_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to appointments (if exists) |
| visit_date | DATE | NOT NULL | Date of visit |
| diagnosis | TEXT | NOT NULL | Medical diagnosis |
| symptoms | TEXT | NULLABLE | Reported symptoms |
| treatment | TEXT | NULLABLE | Treatment plan |
| notes | TEXT | NULLABLE | Additional notes |
| version | INT UNSIGNED | DEFAULT 1 | Version number for versioning |
| previous_version_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Self-referencing for version history |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` Patient
- `belongsTo` Doctor
- `belongsTo` MedicalRecord (previous version)
- `hasMany` Prescription

---

### 6. opd_visits
Outpatient Department (OPD) visit records with queue management.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to patients table |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to doctors table |
| token_number | INT | NULLABLE | Queue token number |
| visit_date | DATE | NOT NULL | Date of visit |
| symptoms | TEXT | NULLABLE | Patient symptoms |
| diagnosis | TEXT | NULLABLE | Doctor's diagnosis |
| visit_type | ENUM | DEFAULT 'New' | New or Follow-up |
| fee | DECIMAL(10,2) | DEFAULT 0 | Visit fee |
| payment_status | ENUM | DEFAULT 'Unpaid' | Paid, Unpaid, Pending |
| status | ENUM | DEFAULT 'scheduled' | scheduled, in_progress, completed, cancelled |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Relationships:**
- `belongsTo` Patient
- `belongsTo` Doctor
- `hasMany` Prescription
- `hasOne` Billing (invoice)

---

### 7. ipd_admissions
Inpatient Department (IPD) admission records.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to patients table |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to doctors table |
| bed_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to beds table |
| admission_date | DATE | NOT NULL | Date of admission |
| discharge_date | DATE | NULLABLE | Date of discharge |
| ward_type | VARCHAR(255) | NOT NULL | Ward type/class |
| bed_number | VARCHAR(255) | NOT NULL | Assigned bed number |
| admission_type | ENUM | DEFAULT 'Planned' | Emergency or Planned |
| admission_reason | TEXT | NULLABLE | Reason for admission |
| symptoms | TEXT | NULLABLE | Patient symptoms |
| diagnosis | TEXT | NULLABLE | Diagnosis |
| discharge_summary | TEXT | NULLABLE | Summary upon discharge |
| total_bill | DECIMAL(10,2) | NULLABLE | Total bill amount |
| status | ENUM | DEFAULT 'admitted' | admitted, discharged, transferred, cancelled |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |
| deleted_at | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Relationships:**
- `belongsTo` Patient
- `belongsTo` Doctor
- `belongsTo` Bed
- `hasMany` IpdNote
- `hasMany` IpdMedication
- `hasMany` IpdLabRequest
- `hasMany` Prescription
- `hasMany` Billing (invoice)

---

## Prescription Tables

### 8. prescriptions
Medical prescriptions linked to visits.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| medical_record_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to medical_records table |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to doctors table |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to patients table |
| opd_visit_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to opd_visits table |
| ipd_admission_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to ipd_admissions table |
| visit_type | VARCHAR(255) | NULLABLE | 'opd' or 'ipd' |
| diagnosis | TEXT | NULLABLE | Prescription diagnosis |
| valid_until | DATE | NULLABLE | Prescription validity date |
| status | ENUM | DEFAULT 'active' | active, dispensed, expired |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` Patient
- `belongsTo` Doctor
- `belongsTo` MedicalRecord
- `belongsTo` OpdVisit
- `belongsTo` IpdAdmission
- `hasMany` PrescriptionItem

---

### 9. prescription_items
Individual medicine items within a prescription.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| prescription_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to prescriptions table |
| medicine_name | VARCHAR(255) | NOT NULL | Name of the medicine |
| dosage | VARCHAR(255) | NOT NULL | Dosage amount (e.g., 500mg) |
| frequency | VARCHAR(255) | NOT NULL | How often (e.g., twice daily) |
| duration | VARCHAR(255) | NOT NULL | Treatment duration |
| instructions | TEXT | NULLABLE | Special instructions |
| price | DECIMAL(10,2) | DEFAULT 0 | Price per unit |
| quantity | INT | DEFAULT 1 | Quantity to dispense |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` Prescription

---

## Billing Tables

### 10. invoices (Billing Model)
Complete billing and invoice management.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to patients table |
| opd_visit_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to opd_visits table |
| ipd_admission_id | BIGINT UNSIGNED | NULLABLE, FOREIGN KEY | Links to ipd_admissions table |
| invoice_number | VARCHAR(255) | UNIQUE, NOT NULL | Unique invoice number |
| charges | DECIMAL(10,2) | DEFAULT 0 | Base charges |
| contractual_adjustments | DECIMAL(10,2) | DEFAULT 0 | Adjustments/ discounts |
| consultation_fee | DECIMAL(10,2) | DEFAULT 0 | Doctor consultation fee |
| lab_charges | DECIMAL(10,2) | DEFAULT 0 | Laboratory test charges |
| medicine_charges | DECIMAL(10,2) | DEFAULT 0 | Medicine/ pharmacy charges |
| procedure_charges | DECIMAL(10,2) | DEFAULT 0 | Medical procedure charges |
| prescription_charges | DECIMAL(10,2) | DEFAULT 0 | Prescription charges |
| subtotal | DECIMAL(10,2) | DEFAULT 0 | Sum before tax |
| tax | DECIMAL(10,2) | DEFAULT 0 | Tax amount |
| total | DECIMAL(10,2) | DEFAULT 0 | Grand total |
| insurance_claim | BOOLEAN | DEFAULT false | Whether insurance is claimed |
| insurance_company | VARCHAR(255) | NULLABLE | Insurance provider name |
| insurance_coverage | DECIMAL(10,2) | DEFAULT 0 | Insurance coverage amount |
| patient_amount | DECIMAL(10,2) | DEFAULT 0 | Amount to be paid by patient |
| paid_amount | DECIMAL(10,2) | DEFAULT 0 | Amount already paid |
| status | ENUM | DEFAULT 'pending' | pending, paid, partially_paid, overdue, cancelled |
| due_date | DATE | NULLABLE | Payment due date |
| payment_method | VARCHAR(255) | NULLABLE | Payment method used |
| bed_type | VARCHAR(255) | NULLABLE | Bed type for IPD |
| bed_days | INT | NULLABLE | Number of bed days |
| bed_charges | DECIMAL(10,2) | DEFAULT 0 | Bed/ room charges |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` Patient
- `belongsTo` OpdVisit
- `belongsTo` IpdAdmission

---

### 11. insurances
Patient insurance policy information.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to patients table |
| provider_name | VARCHAR(255) | NOT NULL | Insurance company name |
| policy_number | VARCHAR(255) | NOT NULL | Policy number |
| coverage_percentage | DECIMAL(5,2) | DEFAULT 0 | Coverage percentage |
| valid_until | DATE | NOT NULL | Policy expiry date |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` Patient

---

## Infrastructure Tables

### 12. beds
Hospital bed inventory and availability.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| ward_type | VARCHAR(255) | NOT NULL | Ward type (e.g., General, ICU, Private) |
| bed_number | VARCHAR(255) | NOT NULL | Bed identifier |
| is_occupied | BOOLEAN | DEFAULT false | Occupancy status |
| floor | VARCHAR(255) | NOT NULL | Floor location |
| price_per_day | DECIMAL(10,2) | DEFAULT 0 | Daily bed charge |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `hasMany` IpdAdmission

---

## IPD Medical Logs Tables

### 13. ipd_notes
Medical notes for IPD patients.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| ipd_admission_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to ipd_admissions table |
| author_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to users table |
| note_type | ENUM | NOT NULL | nurse_chart or doctor_visit |
| notes | TEXT | NOT NULL | Note content |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` IpdAdmission
- `belongsTo` User (author)

---

### 14. ipd_medications
Medication administration records for IPD patients.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| ipd_admission_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to ipd_admissions table |
| medicine_name | VARCHAR(255) | NOT NULL | Medicine name |
| dosage | VARCHAR(255) | NOT NULL | Dosage administered |
| administered_at | DATETIME | NOT NULL | Administration timestamp |
| administered_by | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to users table |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` IpdAdmission
- `belongsTo` User (administered by)

---

### 15. ipd_lab_requests
Laboratory test requests for IPD patients.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| ipd_admission_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to ipd_admissions table |
| test_name | VARCHAR(255) | NOT NULL | Name of the test |
| status | ENUM | DEFAULT 'pending' | pending, completed, cancelled |
| requested_at | DATETIME | NOT NULL | Request timestamp |
| result_notes | TEXT | NULLABLE | Test results |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` IpdAdmission

---

## Communication Tables

### 16. notifications
User notification system.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | FOREIGN KEY, CASCADE | Links to users table |
| title | VARCHAR(255) | NOT NULL | Notification title |
| message | TEXT | NOT NULL | Notification message |
| is_read | BOOLEAN | DEFAULT false | Read status |
| created_at | TIMESTAMP | | Record creation timestamp |
| updated_at | TIMESTAMP | | Record update timestamp |

**Relationships:**
- `belongsTo` User

---

## Laravel System Tables

### 17. sessions
User session management.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | VARCHAR(255) | PRIMARY KEY | Session ID |
| user_id | BIGINT UNSIGNED | NULLABLE | Links to users table |
| ip_address | VARCHAR(45) | NULLABLE | Client IP address |
| user_agent | TEXT | NULLABLE | Browser user agent |
| payload | LONGTEXT | NOT NULL | Session data |
| last_activity | INT | NOT NULL | Last activity timestamp |

---

### 18. password_reset_tokens
Password reset token storage.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| email | VARCHAR(255) | PRIMARY KEY | User email |
| token | VARCHAR(255) | NOT NULL | Reset token |
| created_at | TIMESTAMP | NULLABLE | Token creation time |

---

### 19. cache
Cache storage table.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| key | VARCHAR(255) | PRIMARY KEY | Cache key |
| value | TEXT | NOT NULL | Cached value |
| expiration | INT | NOT NULL | Expiration timestamp |

---

### 20. jobs
Queue job storage.

| Column | Type | Constraints | Description |
|--------|------|------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY | Job ID |
| queue | VARCHAR(255) | NOT NULL | Queue name |
| payload | LONGTEXT | NOT NULL | Job payload |
| attempts | INT UNSIGNED | NOT NULL | Attempt count |
| reserved_at | INT UNSIGNED | NULLABLE | Reserved timestamp |
| available_at | INT UNSIGNED | NOT NULL | Available timestamp |
| created_at | INT UNSIGNED | NOT NULL | Creation timestamp |

---

## Table Relationships Summary

```
┌──────────────┐       ┌──────────────┐       ┌──────────────────┐
│    users     │───────│   doctors    │───────│  availabilities  │
└──────────────┘       └──────────────┘       └──────────────────┘
       │                      │
       │                      │
       ▼                      ▼
┌──────────────┐       ┌──────────────┐
│   patients    │──────│  opd_visits  │
└──────────────┘       └──────────────┘
       │                      │
       │                      │
       ▼                      ▼
┌──────────────┐       ┌──────────────────┐
│ipd_admissions│───────│  medical_records │
└──────────────┘       └──────────────────┘
       │
       ├──────────────┬──────────────┬──────────────┐
       ▼              ▼              ▼              ▼
┌────────────┐ ┌───────────┐ ┌─────────────┐ ┌───────────┐
│ ipd_notes  │ │ipd_meds   │ │ipd_lab_reqs  │ │    beds   │
└────────────┘ └───────────┘ └─────────────┘ └───────────┘

┌──────────────┐       ┌──────────────────┐
│  prescriptions│───────│prescription_items│
└──────────────┘       └──────────────────┘
       │
       ▼
┌──────────────┐       ┌──────────────┐
│    bills     │       │  insurances  │
│  (invoices)  │       └──────────────┘
└──────────────┘
```

## Index Summary

| Table | Indexes |
|-------|---------|
| users | email (unique), role |
| patients | user_id (FK), deleted_at |
| doctors | user_id (FK), license_number (unique), deleted_at |
| availabilities | doctor_id (FK), day_of_week |
| medical_records | patient_id (FK), doctor_id (FK), appointment_id (FK) |
| opd_visits | patient_id (FK), doctor_id (FK), deleted_at |
| ipd_admissions | patient_id (FK), doctor_id (FK), bed_id (FK), deleted_at |
| prescriptions | medical_record_id (FK), doctor_id (FK), patient_id (FK), opd_visit_id (FK), ipd_admission_id (FK) |
| prescription_items | prescription_id (FK) |
| invoices | patient_id (FK), opd_visit_id (FK), ipd_admission_id (FK), invoice_number (unique) |
| insurances | patient_id (FK) |
| beds | (ward_type, bed_number, floor) unique |
| ipd_notes | ipd_admission_id (FK), author_id (FK) |
| ipd_medications | ipd_admission_id (FK), administered_by (FK) |
| ipd_lab_requests | ipd_admission_id (FK) |
| notifications | user_id (FK) |

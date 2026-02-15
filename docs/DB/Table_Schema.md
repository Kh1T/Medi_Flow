users

id, name, email, password, role (derived from Spatie permissions), phone, profile_photo, etc.

patients

id, user_id (nullable, if registered), first_name, last_name, dob, gender, blood_group, phone, email, address, emergency_contact, emergency_phone, created_at, updated_at.

doctors

id, user_id, specialization (text field or FK to specializations), qualification, license_number, experience_years, consultation_fee, is_available (boolean).

availabilities

id, doctor_id, day_of_week (0-6), start_time, end_time, is_recurring (boolean), specific_date (date, null if recurring).

appointments

id, patient_id, doctor_id, appointment_date, appointment_time, status (scheduled, confirmed, checked_in, completed, cancelled, no_show), symptoms (text), notes, created_by (user_id), created_at, updated_at.

medical_records

id, patient_id, doctor_id, visit_date, diagnosis, symptoms, treatment, notes, attachments (media library), version (integer), previous_version_id (self-reference), created_at.

prescriptions

id, medical_record_id, doctor_id, patient_id, diagnosis, valid_until, status (active, dispensed, expired), created_at.

prescription_items

id, prescription_id, medicine_name, dosage, frequency, duration, instructions.

invoices

id, patient_id, appointment_id, invoice_number (unique), subtotal, tax, total, insurance_claim (boolean), insurance_company, insurance_coverage (decimal), patient_amount (decimal), paid_amount, status (pending, paid, partially_paid, cancelled), due_date, created_at.

insurances

id, patient_id, provider_name, policy_number, coverage_percentage, valid_until.

activity_log (Spatie)

logs all model events for audit trail.
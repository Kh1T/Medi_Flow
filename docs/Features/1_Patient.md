👥 PHASE 1: Patient Management
Prerequisites: PHASE 0
Dependencies: None (core feature)

Step 1.1: Patient Model & Migration
bash
php artisan make:model Patient -mcr
Add fields: first_name, last_name, dob, gender, blood_group, phone, email, address, emergency_contact, emergency_phone.

Step 1.2: Patient Factory & Seeder (for testing)
bash
php artisan make:factory PatientFactory --model=Patient
php artisan make:seeder PatientSeeder
Step 1.3: Patient Controller & Views
Implement index, create, store, show, edit, update, destroy.

Use livewire for quick registration component (optional but recommended).

Add search/filter.

Step 1.4: Routes
php
Route::resource('patients', PatientController::class)->middleware(['auth', 'role:Admin,Receptionist,Doctor']);
✅ PHASE 1 COMPLETE – Patients can be created, viewed, edited.
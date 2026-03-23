<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Availability;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\OpdVisit;
use App\Models\Bed;
use App\Models\IpdAdmission;
use App\Models\Billing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Usage from tinker:
     *   (new \Database\Seeders\DatabaseSeeder)->run(count: 20)
     *   (new \Database\Seeders\DatabaseSeeder)->run(count: 50)
     *
     * Or via artisan (uses default count of 15):
     *   php artisan db:seed
     *
     * @param int $count Base number controlling data volume
     */
    public function run(int $count = 15): void
    {
        $faker = \Faker\Factory::create();

        // ── 1. Admin user (skip if exists) ──
        if (!User::where('email', 'admin@mediflow.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@mediflow.com',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'phone' => '+855 12 345 678',
            ]);
        }

        // ── 2. Doctors ──
        $specializations = [
            'Cardiology', 'Neurology', 'Orthopedics', 'Pediatrics',
            'Dermatology', 'General Medicine', 'Gynecology', 'Ophthalmology',
            'ENT', 'Psychiatry', 'Urology', 'Oncology',
        ];
        $doctorCount = max(3, intval($count * 0.3));
        $doctors = [];

        for ($i = 0; $i < $doctorCount; $i++) {
            $user = User::create([
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'phone' => $faker->phoneNumber(),
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialization' => $faker->randomElement($specializations),
                'qualification' => $faker->randomElement(['MBBS', 'MD', 'MS', 'MBBS, MD', 'MBBS, MS']),
                'license_number' => 'LIC-' . strtoupper($faker->unique()->bothify('??####')),
                'experience_years' => $faker->numberBetween(1, 30),
                'consultation_fee' => $faker->randomFloat(2, 30, 200),
                'is_available' => true,
                'available_days' => $faker->randomElements([0, 1, 2, 3, 4, 5, 6], $faker->numberBetween(4, 6)),
            ]);

            // Availabilities for each doctor
            $days = $faker->randomElements([1, 2, 3, 4, 5], $faker->numberBetween(3, 5));
            foreach ($days as $day) {
                Availability::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => $faker->randomElement(['13:00:00', '14:00:00', '16:00:00']),
                    'is_recurring' => true,
                ]);
            }

            $doctors[] = $doctor;
        }

        // ── 3. Receptionist users ──
        for ($i = 0; $i < max(1, intval($count * 0.1)); $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'receptionist',
                'phone' => $faker->phoneNumber(),
            ]);
        }

        // ── 4. Patients ──
        $patients = [];
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        for ($i = 0; $i < $count; $i++) {
            $gender = $faker->randomElement(['Male', 'Female']);
            $firstName = $faker->firstName($gender === 'Male' ? 'male' : 'female');
            $lastName = $faker->lastName();

            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'patient',
                'phone' => $faker->phoneNumber(),
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'dob' => $faker->date('Y-m-d', '-18 years'),
                'gender' => $gender,
                'blood_group' => $faker->randomElement($bloodGroups),
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $faker->address(),
                'emergency_contact' => $faker->name(),
                'emergency_phone' => $faker->phoneNumber(),
            ]);

            $patients[] = $patient;

            // Insurance for ~40% of patients
            if ($faker->boolean(40)) {
                DB::table('insurances')->insert([
                    'patient_id' => $patient->id,
                    'provider_name' => $faker->randomElement(['Forte', 'Cambodia Life', 'BlueCross', 'MediSafe', 'HealthGuard']),
                    'policy_number' => strtoupper($faker->bothify('POL-####-??')),
                    'coverage_percentage' => $faker->randomElement([50, 60, 70, 80, 90]),
                    'valid_until' => $faker->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ── 5. Beds ──
        $wards = ['General', 'ICU', 'Pediatric', 'Maternity', 'Surgical'];
        $floors = ['1st Floor', '2nd Floor', '3rd Floor'];
        $beds = [];

        foreach ($wards as $ward) {
            $bedCount = $ward === 'ICU' ? 5 : $faker->numberBetween(6, 12);
            foreach (range(1, $bedCount) as $num) {
                $bed = Bed::create([
                    'ward_type' => $ward,
                    'bed_number' => $ward[0] . str_pad($num, 3, '0', STR_PAD_LEFT),
                    'is_occupied' => false,
                    'floor' => $faker->randomElement($floors),
                ]);
                $beds[] = $bed;
            }
        }

        // ── 6. Appointments & Medical Records ──
        $appointmentStatuses = ['scheduled', 'completed', 'cancelled', 'no_show'];
        $diagnoses = [
            'Hypertension', 'Type 2 Diabetes', 'Upper respiratory infection',
            'Acute bronchitis', 'Urinary tract infection', 'Back pain',
            'Migraine', 'Allergic rhinitis', 'Gastroenteritis', 'Anemia',
            'Asthma', 'Conjunctivitis', 'Otitis media', 'Dermatitis',
            'Anxiety disorder', 'Viral fever', 'Tonsillitis', 'Sinusitis',
        ];
        $symptoms = [
            'Headache', 'Fever', 'Cough', 'Fatigue', 'Chest pain',
            'Nausea', 'Dizziness', 'Shortness of breath', 'Abdominal pain',
            'Joint pain', 'Sore throat', 'Back pain', 'Skin rash',
        ];
        $medicines = [
            'Amoxicillin 500mg', 'Paracetamol 500mg', 'Ibuprofen 400mg',
            'Metformin 500mg', 'Amlodipine 5mg', 'Omeprazole 20mg',
            'Azithromycin 250mg', 'Cetirizine 10mg', 'Salbutamol Inhaler',
            'Losartan 50mg', 'Atorvastatin 10mg', 'Doxycycline 100mg',
        ];

        $appointmentCount = intval($count * 1.5);
        $createdByUsers = User::whereIn('role', ['admin', 'receptionist'])->pluck('id')->toArray();

        for ($i = 0; $i < $appointmentCount; $i++) {
            $patient = $faker->randomElement($patients);
            $doctor = $faker->randomElement($doctors);
            $status = $faker->randomElement($appointmentStatuses);
            $apptDate = $faker->dateTimeBetween('-6 months', '+1 month');

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => $apptDate->format('Y-m-d'),
                'appointment_time' => $faker->randomElement(['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00']),
                'status' => $status,
                'reason' => $faker->sentence(6),
                'symptoms' => $faker->randomElement($symptoms),
                'notes' => $faker->optional(0.3)->sentence(),
                'created_by' => $faker->randomElement($createdByUsers),
            ]);

            // Medical record for completed appointments
            if ($status === 'completed') {
                $diagnosis = $faker->randomElement($diagnoses);

                $record = MedicalRecord::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'appointment_id' => $appointment->id,
                    'visit_date' => $apptDate->format('Y-m-d'),
                    'diagnosis' => $diagnosis,
                    'symptoms' => implode(', ', $faker->randomElements($symptoms, $faker->numberBetween(1, 3))),
                    'treatment' => $faker->sentence(8),
                    'notes' => $faker->optional(0.5)->paragraph(),
                ]);

                // Prescription for ~70% of completed visits
                if ($faker->boolean(70)) {
                    $prescriptionId = DB::table('prescriptions')->insertGetId([
                        'medical_record_id' => $record->id,
                        'doctor_id' => $doctor->id,
                        'patient_id' => $patient->id,
                        'diagnosis' => $diagnosis,
                        'valid_until' => Carbon::parse($apptDate)->addDays(30)->format('Y-m-d'),
                        'status' => $faker->randomElement(['active', 'dispensed', 'expired']),
                        'created_at' => $apptDate,
                        'updated_at' => $apptDate,
                    ]);

                    $itemCount = $faker->numberBetween(1, 4);
                    for ($j = 0; $j < $itemCount; $j++) {
                        DB::table('prescription_items')->insert([
                            'prescription_id' => $prescriptionId,
                            'medicine_name' => $faker->randomElement($medicines),
                            'dosage' => $faker->randomElement(['1 tablet', '2 tablets', '5ml', '10ml']),
                            'frequency' => $faker->randomElement(['Once daily', 'Twice daily', 'Three times daily', 'Every 8 hours']),
                            'duration' => $faker->randomElement(['3 days', '5 days', '7 days', '14 days', '30 days']),
                            'instructions' => $faker->randomElement(['After meals', 'Before meals', 'With water', 'Before bed', null]),
                            'created_at' => $apptDate,
                            'updated_at' => $apptDate,
                        ]);
                    }
                }
            }
        }

        // ── 7. OPD Visits ──
        $opdCount = intval($count * 1.2);
        $tokenCounter = 1;

        for ($i = 0; $i < $opdCount; $i++) {
            $patient = $faker->randomElement($patients);
            $doctor = $faker->randomElement($doctors);
            $visitDate = $faker->dateTimeBetween('-6 months', 'now');
            $opdStatus = $faker->randomElement(['scheduled', 'in_progress', 'completed', 'completed', 'completed', 'cancelled']);

            OpdVisit::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'token_number' => $tokenCounter++,
                'visit_date' => $visitDate->format('Y-m-d'),
                'symptoms' => implode(', ', $faker->randomElements($symptoms, $faker->numberBetween(1, 3))),
                'diagnosis' => $opdStatus === 'completed' ? $faker->randomElement($diagnoses) : null,
                'visit_type' => $faker->randomElement(['New', 'Follow-up']),
                'fee' => $faker->randomFloat(2, 20, 150),
                'payment_status' => $opdStatus === 'completed' ? $faker->randomElement(['Paid', 'Paid', 'Pending']) : 'Unpaid',
                'status' => $opdStatus,
            ]);
        }

        // ── 8. IPD Admissions ──
        $ipdCount = max(3, intval($count * 0.3));
        $availableBeds = collect($beds)->shuffle();
        $doctorUserIds = User::where('role', 'doctor')->pluck('id')->toArray();
        $allStaffIds = User::whereIn('role', ['admin', 'doctor'])->pluck('id')->toArray();

        for ($i = 0; $i < $ipdCount; $i++) {
            $patient = $faker->randomElement($patients);
            $doctor = $faker->randomElement($doctors);
            $bed = $availableBeds->pop();
            if (!$bed) break;

            $admissionDate = $faker->dateTimeBetween('-4 months', '-3 days');
            $isDischarged = $faker->boolean(60);
            $dischargeDate = $isDischarged ? Carbon::parse($admissionDate)->addDays($faker->numberBetween(2, 21)) : null;
            $status = $isDischarged ? 'discharged' : 'admitted';
            $diagnosis = $faker->randomElement($diagnoses);

            if ($status === 'admitted') {
                $bed->update(['is_occupied' => true]);
            }

            $admission = IpdAdmission::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'bed_id' => $bed->id,
                'admission_date' => $admissionDate->format('Y-m-d'),
                'discharge_date' => $dischargeDate?->format('Y-m-d'),
                'ward_type' => $bed->ward_type,
                'bed_number' => $bed->bed_number,
                'admission_type' => $faker->randomElement(['Emergency', 'Planned']),
                'admission_reason' => $faker->sentence(6),
                'symptoms' => implode(', ', $faker->randomElements($symptoms, $faker->numberBetween(1, 3))),
                'diagnosis' => $diagnosis,
                'discharge_summary' => $isDischarged ? $faker->paragraph() : null,
                'total_bill' => $isDischarged ? $faker->randomFloat(2, 500, 10000) : null,
                'status' => $status,
            ]);

            // IPD Notes
            $noteCount = $faker->numberBetween(1, 5);
            for ($j = 0; $j < $noteCount; $j++) {
                DB::table('ipd_notes')->insert([
                    'ipd_admission_id' => $admission->id,
                    'author_id' => $faker->randomElement($allStaffIds),
                    'note_type' => $faker->randomElement(['nurse_chart', 'doctor_visit']),
                    'notes' => $faker->paragraph(),
                    'created_at' => Carbon::parse($admissionDate)->addDays($j),
                    'updated_at' => Carbon::parse($admissionDate)->addDays($j),
                ]);
            }

            // IPD Medications
            $medCount = $faker->numberBetween(1, 4);
            for ($j = 0; $j < $medCount; $j++) {
                DB::table('ipd_medications')->insert([
                    'ipd_admission_id' => $admission->id,
                    'medicine_name' => $faker->randomElement($medicines),
                    'dosage' => $faker->randomElement(['1 tablet', '2 tablets', '5ml', '10ml', '1 injection']),
                    'administered_at' => Carbon::parse($admissionDate)->addDays($j)->addHours($faker->numberBetween(6, 20)),
                    'administered_by' => $faker->randomElement($allStaffIds),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // IPD Lab Requests
            if ($faker->boolean(60)) {
                $labCount = $faker->numberBetween(1, 3);
                $testNames = ['Complete Blood Count', 'Blood Sugar', 'Urinalysis', 'X-Ray', 'MRI', 'CT Scan', 'Liver Function Test', 'Kidney Function Test', 'ECG', 'Lipid Profile'];
                for ($j = 0; $j < $labCount; $j++) {
                    $labStatus = $isDischarged ? $faker->randomElement(['completed', 'completed', 'cancelled']) : $faker->randomElement(['pending', 'completed']);
                    DB::table('ipd_lab_requests')->insert([
                        'ipd_admission_id' => $admission->id,
                        'test_name' => $faker->randomElement($testNames),
                        'status' => $labStatus,
                        'requested_at' => Carbon::parse($admissionDate)->addDays($faker->numberBetween(0, 3)),
                        'result_notes' => $labStatus === 'completed' ? $faker->sentence() : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // ── 9. Invoices ──
        // OPD Invoices
        $opdVisits = OpdVisit::where('status', 'completed')->get();
        $invoiceNum = 1;

        foreach ($opdVisits as $visit) {
            $charges = $visit->fee;
            $adjustment = $faker->randomFloat(2, 0, $charges * 0.1);
            $subtotal = $charges - $adjustment;
            $tax = round($subtotal * 0.05, 2);
            $total = round($subtotal + $tax, 2);
            $hasInsurance = $faker->boolean(30);
            $coverage = $hasInsurance ? round($total * $faker->randomElement([0.5, 0.6, 0.7, 0.8]), 2) : 0;
            $patientAmount = round($total - $coverage, 2);
            $isPaid = $visit->payment_status === 'Paid';
            $createdAt = $visit->visit_date;

            Billing::create([
                'patient_id' => $visit->patient_id,
                'opd_visit_id' => $visit->id,
                'invoice_number' => 'INV-' . str_pad($invoiceNum++, 6, '0', STR_PAD_LEFT),
                'charges' => $charges,
                'contractual_adjustments' => $adjustment,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'insurance_claim' => $hasInsurance,
                'insurance_company' => $hasInsurance ? $faker->randomElement(['Forte', 'BlueCross', 'MediSafe']) : null,
                'insurance_coverage' => $coverage,
                'patient_amount' => $patientAmount,
                'paid_amount' => $isPaid ? $patientAmount : 0,
                'status' => $isPaid ? 'paid' : $faker->randomElement(['pending', 'overdue']),
                'due_date' => Carbon::parse($createdAt)->addDays(30)->format('Y-m-d'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // IPD Invoices
        $ipdAdmissions = IpdAdmission::where('status', 'discharged')->get();

        foreach ($ipdAdmissions as $admission) {
            $charges = $admission->total_bill ?? $faker->randomFloat(2, 500, 8000);
            $adjustment = $faker->randomFloat(2, 0, $charges * 0.1);
            $subtotal = $charges - $adjustment;
            $tax = round($subtotal * 0.05, 2);
            $total = round($subtotal + $tax, 2);
            $hasInsurance = $faker->boolean(40);
            $coverage = $hasInsurance ? round($total * $faker->randomElement([0.5, 0.6, 0.7, 0.8]), 2) : 0;
            $patientAmount = round($total - $coverage, 2);
            $isPaid = $faker->boolean(70);
            $createdAt = $admission->discharge_date;

            Billing::create([
                'patient_id' => $admission->patient_id,
                'ipd_admission_id' => $admission->id,
                'invoice_number' => 'INV-' . str_pad($invoiceNum++, 6, '0', STR_PAD_LEFT),
                'charges' => $charges,
                'contractual_adjustments' => $adjustment,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'insurance_claim' => $hasInsurance,
                'insurance_company' => $hasInsurance ? $faker->randomElement(['Cambodia Life', 'HealthGuard', 'BlueCross']) : null,
                'insurance_coverage' => $coverage,
                'patient_amount' => $patientAmount,
                'paid_amount' => $isPaid ? $patientAmount : round($patientAmount * $faker->randomFloat(2, 0.2, 0.5), 2),
                'status' => $isPaid ? 'paid' : 'partially_paid',
                'due_date' => Carbon::parse($createdAt)->addDays(30)->format('Y-m-d'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        echo "\n✓ Seeded with count={$count}:\n";
        echo "  • " . User::count() . " users\n";
        echo "  • " . Doctor::count() . " doctors\n";
        echo "  • " . Patient::count() . " patients\n";
        echo "  • " . Appointment::count() . " appointments\n";
        echo "  • " . OpdVisit::count() . " OPD visits\n";
        echo "  • " . IpdAdmission::count() . " IPD admissions\n";
        echo "  • " . Bed::count() . " beds\n";
        echo "  • " . Billing::count() . " invoices\n";
    }
}

<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\OpdVisit;
use App\Models\Patient;
use App\Models\Insurance;
use App\Models\IpdAdmission;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Calculate insurance coverage for a patient
     * 
     * @param int $patientId
     * @param float $total
     * @return array ['insurance_coverage' => float, 'patient_responsibility' => float, 'insurance' => Insurance|null]
     */
    public function calculateInsuranceCoverage(int $patientId, float $total): array
    {
        $patient = Patient::findOrFail($patientId);
        $insurance = $patient->insurances()
            ->where('valid_until', '>=', now()->toDateString())
            ->first();

        if (!$insurance) {
            return [
                'insurance_coverage' => 0,
                'patient_responsibility' => $total,
                'insurance' => null,
            ];
        }

        $coverageAmount = ($total * $insurance->coverage_percentage) / 100;
        $patientResponsibility = $total - $coverageAmount;

        return [
            'insurance_coverage' => round($coverageAmount, 2),
            'patient_responsibility' => round($patientResponsibility, 2),
            'insurance' => $insurance,
        ];
    }

    /**
     * Generate OPD Invoice with all charges
     * 
     * @param OpdVisit $visit
     * @param array $billingData [
     *     'consultation_fee' => float,
     *     'lab_charges' => float,
     *     'medicine_cost' => float,
     *     'procedure_charges' => float,
     *     'discount' => float,
     *     'apply_insurance' => boolean,
     *     'payment_method' => string,
     *     'paid_amount' => float,
     * ]
     * @return Billing
     */
    public function generateOpdInvoice(OpdVisit $visit, array $billingData): Billing
    {
        return DB::transaction(function () use ($visit, $billingData) {
            // Extract billing components
            $consultationFee = floatval($billingData['consultation_fee'] ?? 0);
            $labCharges = floatval($billingData['lab_charges'] ?? 0);
            $medicineCost = floatval($billingData['medicine_cost'] ?? 0);
            $procedureCharges = floatval($billingData['procedure_charges'] ?? 0);
            $discount = floatval($billingData['discount'] ?? 0);
            $applyInsurance = $billingData['apply_insurance'] ?? false;
            $paymentMethod = $billingData['payment_method'] ?? 'cash';
            $paidAmount = floatval($billingData['paid_amount'] ?? 0);

            // Calculate subtotal (before discount and insurance)
            $subtotal = $consultationFee + $labCharges + $medicineCost + $procedureCharges;

            // Apply discount
            $afterDiscount = $subtotal - $discount;

            // Calculate insurance if applicable
            $insuranceCoverage = 0;
            $insuranceCompany = null;
            $insuranceClaim = false;

            if ($applyInsurance) {
                $insuranceResult = $this->calculateInsuranceCoverage($visit->patient_id, $afterDiscount);
                $insuranceCoverage = $insuranceResult['insurance_coverage'];
                $insuranceCompany = $insuranceResult['insurance']?->provider_name;
                $insuranceClaim = $insuranceCoverage > 0;
            }

            // Calculate patient amount (final amount patient owes)
            $patientAmount = max(0, $afterDiscount - $insuranceCoverage);

            // Determine status based on payment
            $status = 'pending';
            if ($paidAmount >= $patientAmount && $patientAmount > 0) {
                $status = 'paid';
            } elseif ($paidAmount > 0 && $paidAmount < $patientAmount) {
                $status = 'partially_paid';
            }

            // Generate invoice number
            $invoiceNumber = 'INV-OPD-' . strtoupper(str()->random(6));

            // Create invoice
            $invoice = Billing::create([
                'patient_id' => $visit->patient_id,
                'opd_visit_id' => $visit->id,
                'invoice_number' => $invoiceNumber,
                'charges' => $subtotal,
                'contractual_adjustments' => $discount,
                'subtotal' => $afterDiscount,
                'tax' => 0, // Can be added if needed
                'total' => $patientAmount,
                'insurance_claim' => $insuranceClaim,
                'insurance_company' => $insuranceCompany,
                'insurance_coverage' => $insuranceCoverage,
                'patient_amount' => $patientAmount,
                'paid_amount' => $paidAmount,
                'status' => $status,
                'due_date' => now()->addDays(7)->toDateString(),
            ]);

            // Update OPD visit payment status if fully paid
            if ($status === 'paid') {
                $visit->update(['payment_status' => 'Paid']);
            } elseif ($paidAmount > 0) {
                $visit->update(['payment_status' => 'Pending']);
            }

            return $invoice;
        });
    }

    /**
     * Process payment for an invoice
     * 
     * @param Billing $invoice
     * @param float $amount
     * @param string $method
     * @return Billing
     */
    public function processPayment(Billing $invoice, float $amount, string $method = 'cash'): Billing
    {
        $newPaidAmount = $invoice->paid_amount + $amount;
        
        // Determine new status
        $status = $invoice->status;
        if ($newPaidAmount >= $invoice->patient_amount) {
            $status = 'paid';
        } elseif ($newPaidAmount > 0) {
            $status = 'partially_paid';
        }

        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'status' => $status,
        ]);

        // Update OPD visit payment status if linked
        if ($invoice->opd_visit_id) {
            $visit = OpdVisit::find($invoice->opd_visit_id);
            if ($visit) {
                $visitStatus = $status === 'paid' ? 'Paid' : ($status === 'partially_paid' ? 'Pending' : $visit->payment_status);
                $visit->update(['payment_status' => $visitStatus]);
            }
        }

        return $invoice->fresh();
    }

    /**
     * Calculate billing breakdown summary
     * 
     * @param array $billingData
     * @return array
     */
    public function calculateBillingSummary(array $billingData): array
    {
        $consultationFee = floatval($billingData['consultation_fee'] ?? 0);
        $labCharges = floatval($billingData['lab_charges'] ?? 0);
        $medicineCost = floatval($billingData['medicine_cost'] ?? 0);
        $procedureCharges = floatval($billingData['procedure_charges'] ?? 0);
        $discount = floatval($billingData['discount'] ?? 0);
        $applyInsurance = $billingData['apply_insurance'] ?? false;

        $subtotal = $consultationFee + $labCharges + $medicineCost + $procedureCharges;
        $afterDiscount = $subtotal - $discount;

        $insuranceCoverage = 0;
        $patientAmount = $afterDiscount;

        if ($applyInsurance && isset($billingData['patient_id'])) {
            $insuranceResult = $this->calculateInsuranceCoverage($billingData['patient_id'], $afterDiscount);
            $insuranceCoverage = $insuranceResult['insurance_coverage'];
            $patientAmount = $insuranceResult['patient_responsibility'];
        }

        return [
            'consultation_fee' => $consultationFee,
            'lab_charges' => $labCharges,
            'medicine_cost' => $medicineCost,
            'procedure_charges' => $procedureCharges,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'after_discount' => $afterDiscount,
            'insurance_coverage' => $insuranceCoverage,
            'final_bill' => max(0, $patientAmount),
        ];
    }

    /**
     * Generate IPD Invoice with bed charges based on bed type and days stayed
     * 
     * @param IpdAdmission $admission
     * @param array $billingData [
     *     'bed_charges' => float,
     *     'medicine_charges' => float,
     *     'misc_charges' => float,
     *     'discount' => float,
     *     'apply_insurance' => boolean,
     *     'payment_method' => string,
     *     'paid_amount' => float,
     * ]
     * @return Billing
     */
    public function generateIpdInvoice(IpdAdmission $admission, array $billingData): Billing
    {
        return DB::transaction(function () use ($admission, $billingData) {
            // Get bed information
            $bed = $admission->bed;
            $bedType = $admission->ward_type ?? 'General';
            $bedDays = $admission->admission_date->diffInDays($admission->discharge_date ?? now()) ?: 1;
            
            // Get bed price per day (from bed model)
            $bedPricePerDay = $bed?->price_per_day ?? $this->getDefaultBedRate($bedType);
            $bedCharges = floatval($billingData['bed_charges'] ?? ($bedDays * $bedPricePerDay));
            
            // Extract other billing components
            $medicineCharges = floatval($billingData['medicine_charges'] ?? 0);
            $miscCharges = floatval($billingData['misc_charges'] ?? 0);
            $discount = floatval($billingData['discount'] ?? 0);
            $applyInsurance = $billingData['apply_insurance'] ?? false;
            $paymentMethod = $billingData['payment_method'] ?? 'cash';
            $paidAmount = floatval($billingData['paid_amount'] ?? 0);

            // Calculate subtotal (before discount and insurance)
            $subtotal = $bedCharges + $medicineCharges + $miscCharges;

            // Apply discount
            $afterDiscount = $subtotal - $discount;

            // Calculate insurance if applicable
            $insuranceCoverage = 0;
            $insuranceCompany = null;
            $insuranceClaim = false;

            if ($applyInsurance) {
                $insuranceResult = $this->calculateInsuranceCoverage($admission->patient_id, $afterDiscount);
                $insuranceCoverage = $insuranceResult['insurance_coverage'];
                $insuranceCompany = $insuranceResult['insurance']?->provider_name;
                $insuranceClaim = $insuranceCoverage > 0;
            }

            // Calculate patient amount (final amount patient owes)
            $patientAmount = max(0, $afterDiscount - $insuranceCoverage);

            // Calculate tax (example: 5% tax)
            $tax = $patientAmount * 0.05;
            $total = $patientAmount + $tax;

            // Determine status based on payment
            $status = 'pending';
            if ($paidAmount >= $total && $total > 0) {
                $status = 'paid';
            } elseif ($paidAmount > 0 && $paidAmount < $total) {
                $status = 'partially_paid';
            }

            // Generate invoice number
            $invoiceNumber = 'INV-IPD-' . strtoupper(str()->random(6));

            // Create invoice
            $invoice = Billing::create([
                'patient_id' => $admission->patient_id,
                'ipd_admission_id' => $admission->id,
                'invoice_number' => $invoiceNumber,
                'charges' => $subtotal,
                'contractual_adjustments' => $discount,
                'subtotal' => $afterDiscount,
                'tax' => $tax,
                'total' => $total,
                'insurance_claim' => $insuranceClaim,
                'insurance_company' => $insuranceCompany,
                'insurance_coverage' => $insuranceCoverage,
                'patient_amount' => $patientAmount,
                'paid_amount' => $paidAmount,
                'status' => $status,
                'due_date' => now()->addDays(7)->toDateString(),
                'bed_type' => $bedType,
                'bed_days' => $bedDays,
                'bed_charges' => $bedCharges,
            ]);

            return $invoice;
        });
    }

    /**
     * Get default bed rate based on ward type
     * 
     * @param string $wardType
     * @return float
     */
    public function getDefaultBedRate(string $wardType): float
    {
        $rates = [
            'General' => 500,
            'Private' => 1500,
            'ICU' => 5000,
            'VIP' => 3000,
            'Semi-Private' => 1000,
        ];

        return $rates[$wardType] ?? 1000;
    }

    /**
     * Calculate IPD bed charges summary
     * 
     * @param IpdAdmission $admission
     * @return array
     */
    public function calculateIpdBedSummary(IpdAdmission $admission): array
    {
        $bed = $admission->bed;
        $bedType = $admission->ward_type ?? 'General';
        $bedDays = $admission->admission_date->diffInDays($admission->discharge_date ?? now()) ?: 1;
        $bedPricePerDay = $bed?->price_per_day ?? $this->getDefaultBedRate($bedType);
        $bedCharges = $bedDays * $bedPricePerDay;

        return [
            'bed_type' => $bedType,
            'bed_days' => $bedDays,
            'price_per_day' => $bedPricePerDay,
            'bed_charges' => $bedCharges,
        ];
    }
}

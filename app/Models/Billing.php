<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'opd_visit_id',
        'ipd_admission_id',
        'invoice_number',
        'charges',
        'contractual_adjustments',
        'insurance_coverage',
        'patient_amount',
        'subtotal',
        'tax',
        'total',
        'insurance_claim',
        'insurance_company',
        'paid_amount',
        'status',
        'due_date'
    ];

    protected function casts(): array
    {
        return [
            'charges' => 'decimal:2',
            'contractual_adjustments' => 'decimal:2',
            'insurance_coverage' => 'decimal:2',
            'patient_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'insurance_claim' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function opdVisit()
    {
        return $this->belongsTo(OpdVisit::class, 'opd_visit_id');
    }

    public function ipdAdmission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    /**
     * Check if invoice is fully paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice has pending balance
     */
    public function hasPendingBalance(): bool
    {
        return $this->patient_amount > $this->paid_amount;
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalance(): float
    {
        return max(0, $this->patient_amount - $this->paid_amount);
    }
}

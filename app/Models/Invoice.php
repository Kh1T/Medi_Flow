<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

class Invoice extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'opd_visit_id',
        'ipd_admission_id',
        'invoice_number',
        'charges',
        'contractual_adjustments',
        'subtotal',
        'tax',
        'total',
        'insurance_claim',
        'insurance_company',
        'insurance_coverage',
        'patient_amount',
        'paid_amount',
        'status',
        'due_date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

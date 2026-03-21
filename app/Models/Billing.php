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
        'due_date',
        'payment_method',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
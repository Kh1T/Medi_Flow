<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

class Invoice extends Model
{
    protected $fillable = [
        'patient_id',
        'opd_visit_id',
        'ipd_admission_id',
        'subtotal',
        'tax',
        'total_amount',
        'due_date',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

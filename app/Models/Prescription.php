<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medical_record_id',
        'opd_visit_id',
        'ipd_admission_id',
        'visit_type',
        'diagnosis',
        'valid_until',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function opdVisit()
    {
        return $this->belongsTo(OpdVisit::class, 'opd_visit_id');
    }

    public function ipdAdmission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Alias for prescriptionItems for backward compatibility
     */
    public function items(): HasMany
    {
        return $this->prescriptionItems();
    }

    /**
     * Calculate total cost of all prescription items
     */
    public function getTotalCost(): float
    {
        return $this->prescriptionItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}

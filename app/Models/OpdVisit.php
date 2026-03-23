<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpdVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'token_number',
        'visit_date',
        'symptoms',
        'diagnosis',
        'visit_type',
        'fee',
        'payment_status',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'fee' => 'decimal:2',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'opd_visit_id');
    }

    public function billing()
    {
        return $this->hasOne(Billing::class, 'opd_visit_id');
    }
}

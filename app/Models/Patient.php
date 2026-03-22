<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'blood_group',
        'phone',
        'email',
        'address',
        'emergency_contact',
        'emergency_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function opdVisits()
    {
        return $this->hasMany(OpdVisit::class);
    }

    public function ipdAdmissions()
    {
        return $this->hasMany(IpdAdmission::class);
    }

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }

    /**
     * Get the active (valid) insurance for the patient
     */
    public function activeInsurance()
    {
        return $this->hasOne(Insurance::class)->where('valid_until', '>=', now()->toDateString());
    }

    /**
     * Check if patient has valid insurance
     */
    public function hasValidInsurance(): bool
    {
        return $this->insurances()->where('valid_until', '>=', now()->toDateString())->exists();
    }
}

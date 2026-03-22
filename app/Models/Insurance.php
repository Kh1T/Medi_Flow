<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'provider_name',
        'policy_number',
        'coverage_percentage',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'coverage_percentage' => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if insurance is valid (not expired)
     */
    public function isValid(): bool
    {
        return $this->valid_until >= now()->toDateString();
    }

    /**
     * Calculate coverage amount for a given total
     */
    public function calculateCoverage(float $total): float
    {
        return ($total * $this->coverage_percentage) / 100;
    }
}

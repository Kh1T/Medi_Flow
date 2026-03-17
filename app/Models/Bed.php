<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_type',
        'bed_number',
        'is_occupied',
        'floor',
    ];

    protected function casts(): array
    {
        return [
            'is_occupied' => 'boolean',
        ];
    }

    public function ipdAdmissions()
    {
        return $this->hasMany(IpdAdmission::class);
    }
}

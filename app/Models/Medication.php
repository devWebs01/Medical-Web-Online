<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dosage',
        'price',
        'category',
        // 'unit',
    ];

    /**
     * Get all of the prescriptions for the MedicalRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}

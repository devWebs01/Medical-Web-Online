<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'medicine_id',
        'quantity',
        'frequency',
        'duration',
        'note',
    ];

    /**
     * Get the medical_record that owns the Prescription
     */
    public function medical_record(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Get the medication that owns the Prescription
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class, 'medicine_id');
    }
}

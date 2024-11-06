<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'complaint',
        'diagnosis',
        'physical_exam',
        'recommendation',
        'type',
        'status',
    ];

    /**
     * Get the appointment that owns the MedicalRecord
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get all of the prescriptions for the MedicalRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get all of the inpatient_records for the MedicalRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inpatientRecords(): HasMany
    {
        return $this->hasMany(InpatientRecord::class);
    }
}

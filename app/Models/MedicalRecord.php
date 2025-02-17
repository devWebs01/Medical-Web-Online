<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
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
    public function inpatientRecord(): HasOne
    {
        return $this->hasOne(InpatientRecord::class);
    }

    /**
     * Get the patient that owns the MedicalRecord
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function paymentRecord(): hasOne
    {
        return $this->hasOne(PaymentRecord::class);
    }
}

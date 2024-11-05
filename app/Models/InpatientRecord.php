<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InpatientRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'room_id',
        'admission_date',
        'discharge_date',
        'doctor_notes',
        'status',
    ];

    /**
     * Get the medicalRecord that owns the medicalRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }
    /**
     * Get the room that owns the room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(room::class, 'room_id');
    }
}

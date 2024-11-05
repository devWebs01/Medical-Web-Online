<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'price',
        'availability',
    ];

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

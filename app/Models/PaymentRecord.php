<?php

namespace App\Models;

use App\Models\Medication;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'total_amount',
        'payment_date',
        'status',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function medications(): BelongsToMany
    {
        return $this->belongsToMany(Medication::class, 'medication_payment')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
    public function additionalFees(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalFees::class, 'payment_additional_fee');
    }
}

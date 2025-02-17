<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdditionalFees extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cost'];

    /**
     * Relasi Many-to-Many dengan PaymentRecord.
     */
    public function paymentRecords(): BelongsToMany
    {
        return $this->belongsToMany(PaymentRecord::class, 'payment_additional_fee');
    }
}

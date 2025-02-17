<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'invoice',
    ];

    /**
     * Boot method untuk membuat invoice otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentRecord) {
            $paymentRecord->invoice = self::generateInvoiceNumber();
        });
    }

    /**
     * Generate nomor invoice secara otomatis.
     */
    public static function generateInvoiceNumber()
    {
        $latestInvoice = self::latest()->first();
        $nextNumber = $latestInvoice ? intval(substr($latestInvoice->invoice, -4)) + 1 : 1;

        return 'INV-'.date('Y').'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

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

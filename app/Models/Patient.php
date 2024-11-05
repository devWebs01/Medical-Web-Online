<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'dob',
        'address',
        'phone',
    ];

    /**
     * Get all of the Appointments for the User
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

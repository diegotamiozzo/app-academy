<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'document',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function licensePurchases(): HasMany
    {
        return $this->hasMany(LicensePurchase::class);
    }

    public function accessCodes(): HasMany
    {
        return $this->hasMany(AccessCode::class);
    }
}

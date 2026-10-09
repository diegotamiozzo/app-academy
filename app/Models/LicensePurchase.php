<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LicensePurchase extends Model
{
    protected $fillable = [
        'company_id',
        'role',
        'quantity',
        'amount',
        'purchased_at',
    ];

    protected $casts = [
        'role' => Role::class,
        'purchased_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function accessCodes(): HasMany
    {
        return $this->hasMany(AccessCode::class);
    }

    public function generateAccessCodes(?int $count = null): Collection
    {
        $count ??= $this->quantity;

        $codes = collect();

        for ($index = 0; $index < $count; $index++) {
            $code = strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));

            $codes->push(AccessCode::firstOrCreate([
                'company_id' => $this->company_id,
                'license_purchase_id' => $this->id,
                'code' => $code,
            ], [
                'role' => $this->role->value,
            ]));
        }

        return $codes;
    }
}

<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccessCode extends Model
{
    protected $fillable = [
        'company_id',
        'license_purchase_id',
        'code',
        'role',
        'user_id',
        'redeemed_at',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'role' => Role::class,
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(LicensePurchase::class, 'license_purchase_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRedeemable(): bool
    {
        if ($this->redeemed_at !== null || $this->revoked_at !== null) {
            return false;
        }

        if ($this->expires_at !== null && now()->greaterThanOrEqualTo($this->expires_at)) {
            return false;
        }

        return true;
    }

    public static function redeemForUser(User $user, string $code): self
    {
        return DB::transaction(function () use ($user, $code) {
            $accessCode = self::query()
                ->where('code', strtoupper(trim($code)))
                ->lockForUpdate()
                ->first();

            if (! $accessCode || ! $accessCode->isRedeemable()) {
                throw ValidationException::withMessages([
                    'access_code' => 'Codigo de acesso invalido, expirado ou ja utilizado.',
                ]);
            }

            if ($accessCode->user_id !== null) {
                throw ValidationException::withMessages([
                    'access_code' => 'Codigo de acesso invalido, expirado ou ja utilizado.',
                ]);
            }

            $user->update([
                'company_id' => $accessCode->company_id,
                'role' => $accessCode->role->value,
            ]);

            $accessCode->update([
                'user_id' => $user->id,
                'redeemed_at' => now(),
            ]);

            return $accessCode->fresh();
        });
    }
}

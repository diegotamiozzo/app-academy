<?php

use App\Enums\Role;
use App\Models\AccessCode;
use App\Models\Company;
use App\Models\LicensePurchase;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Validation\ValidationException;

it('seeds a verified instructor account for the demo company', function () {
    $this->seed(DatabaseSeeder::class);

    $company = Company::where('name', 'Demo Company')->firstOrFail();

    $this->assertDatabaseHas('users', [
        'email' => 'instructor@academy.test',
        'company_id' => $company->id,
        'role' => Role::Instructor->value,
    ]);
});

it('allows admins to access the admin route', function () {
    $admin = User::factory()->create([
        'role' => Role::Admin,
        'company_id' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin'))
        ->assertOk();
});

it('blocks non-admin users from the admin route', function () {
    $user = User::factory()->create([
        'role' => Role::User,
    ]);

    $this->actingAs($user)
        ->get(route('admin'))
        ->assertForbidden();
});

it('redeems access codes and sets company and role from the code', function () {
    $company = Company::create([
        'name' => 'Acme Academy',
    ]);

    $purchase = LicensePurchase::create([
        'company_id' => $company->id,
        'role' => 'user',
        'quantity' => 1,
        'amount' => 199.00,
        'purchased_at' => now(),
    ]);

    AccessCode::create([
        'company_id' => $company->id,
        'license_purchase_id' => $purchase->id,
        'code' => 'A1B2-C3D4-E5F6',
        'role' => 'user',
    ]);

    $user = User::factory()->create([
        'email' => 'new-user@example.com',
        'company_id' => null,
        'role' => Role::User,
    ]);

    $accessCode = AccessCode::redeemForUser($user, 'A1B2-C3D4-E5F6');

    expect($accessCode->user_id)->toBe($user->id)
        ->and($user->fresh()->company_id)->toBe($company->id)
        ->and($user->fresh()->role)->toBe(Role::User);
});

it('rejects invalid access codes with the generic validation message', function () {
    $user = User::factory()->create();

    expect(fn () => AccessCode::redeemForUser($user, 'INVALID'))
        ->toThrow(ValidationException::class);
});

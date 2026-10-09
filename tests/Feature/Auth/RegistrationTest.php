<?php

use App\Enums\Role;
use App\Models\AccessCode;
use App\Models\Company;
use App\Models\LicensePurchase;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $company = Company::create(['name' => 'Test Company']);
    $purchase = LicensePurchase::create([
        'company_id' => $company->id,
        'role' => Role::User->value,
        'quantity' => 1,
        'amount' => 199.00,
        'purchased_at' => now(),
    ]);

    AccessCode::create([
        'company_id' => $company->id,
        'license_purchase_id' => $purchase->id,
        'code' => 'A1B2-C3D4-E5F6',
        'role' => Role::User->value,
    ]);

    $response = Volt::test('auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('access_code', 'A1B2-C3D4-E5F6')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'company_id' => $company->id,
        'role' => Role::User->value,
    ]);
});

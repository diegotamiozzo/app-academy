<?php

use App\Enums\Role;
use App\Models\AccessCode;
use App\Models\Company;
use App\Models\LicensePurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function (Request $request) {
    return match ($request->user()->role) {
        Role::Admin => redirect()->route('admin'),
        Role::Instructor => redirect()->route('instructor.dashboard'),
        Role::User => redirect()->route('user.dashboard'),
    };
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('admin', function () {
    return view('dashboards.admin', [
        'companyCount' => Company::count(),
        'userCount' => User::count(),
        'purchaseCount' => LicensePurchase::count(),
        'accessCodeCount' => AccessCode::count(),
    ]);
})
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin');

Route::get('instructor/dashboard', function (Request $request) {
    return view('dashboards.instructor', [
        'companyName' => $request->user()->company?->name,
    ]);
})
    ->middleware(['auth', 'verified', 'role:instructor'])
    ->name('instructor.dashboard');

Route::get('user/dashboard', function (Request $request) {
    return view('dashboards.user', [
        'companyName' => $request->user()->company?->name,
    ]);
})
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('user.dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';

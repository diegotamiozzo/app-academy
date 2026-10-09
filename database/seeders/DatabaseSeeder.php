<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate([
            'name' => 'Demo Company',
        ]);

        User::firstOrCreate([
            'email' => 'admin@academy.test',
        ], [
            'name' => 'Platform Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => null,
            'role' => Role::Admin,
        ]);

        User::firstOrCreate([
            'email' => 'instructor@academy.test',
        ], [
            'name' => 'Demo Instructor',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => $company->id,
            'role' => Role::Instructor,
        ]);

        User::firstOrCreate([
            'email' => 'test@academy.test',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => $company->id,
            'role' => Role::User,
        ]);
    }
}

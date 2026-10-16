<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\Company;
use App\Models\LicensePurchase;
use Illuminate\Console\Command;

class GenerateAccessCodes extends Command
{
    protected $signature = 'access-codes:generate {company_id} {role} {purchase_id} {--count=}';

    protected $description = 'Generate access codes for a purchase';

    public function handle(): int
    {
        $company = Company::findOrFail((int) $this->argument('company_id'));
        $purchase = LicensePurchase::findOrFail((int) $this->argument('purchase_id'));
        $role = Role::tryFrom($this->argument('role'));

        if ($role === null) {
            $this->error('Role must be one of: '.implode(', ', array_map(fn (Role $item) => $item->value, Role::cases())));

            return self::FAILURE;
        }

        if ($company->id !== $purchase->company_id || $purchase->role->value !== $role->value) {
            $this->error('The purchase does not match the company or role provided.');

            return self::FAILURE;
        }

        $count = (int) ($this->option('count') ?? $purchase->quantity);
        $codes = $purchase->generateAccessCodes($count);

        $this->info('Generated '.$codes->count().' access codes for '.$company->name.' as '.$role->value.'.');

        return self::SUCCESS;
    }
}

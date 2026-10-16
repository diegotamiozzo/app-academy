<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->nullable()->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('role', array_map(fn (Role $role) => $role->value, Role::cases()))->default(Role::User->value);
            $table->index(['company_id', 'role']);
        });

        Schema::create('license_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['instructor', 'user']);
            $table->unsignedInteger('quantity');
            $table->decimal('amount', 12, 2)->nullable();
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('access_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->enum('role', ['instructor', 'user']);
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_codes');
        Schema::dropIfExists('license_purchases');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'role']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'role']);
        });

        Schema::dropIfExists('companies');
    }
};

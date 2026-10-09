<?php

use App\Enums\Role;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create([
        'role' => Role::User,
    ]);
    $this->actingAs($user);

    $this->get('/dashboard')
        ->assertRedirect(route('user.dashboard'));
});

test('each role is redirected to its own dashboard', function (Role $role, string $dashboardRoute) {
    $user = User::factory()->create([
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route($dashboardRoute));
})->with([
    'admin' => [Role::Admin, 'admin'],
    'instructor' => [Role::Instructor, 'instructor.dashboard'],
    'user' => [Role::User, 'user.dashboard'],
]);

test('each role can only access its own dashboard', function (Role $role, string $ownRoute, string $otherRoute, string $heading) {
    $user = User::factory()->create([
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->get(route($ownRoute))
        ->assertOk()
        ->assertSee($heading);

    $this->get(route($otherRoute))
        ->assertForbidden();
})->with([
    'admin' => [Role::Admin, 'admin', 'instructor.dashboard', 'Painel do administrador'],
    'instructor' => [Role::Instructor, 'instructor.dashboard', 'user.dashboard', 'Painel do instrutor'],
    'user' => [Role::User, 'user.dashboard', 'admin', 'Minha aprendizagem'],
]);

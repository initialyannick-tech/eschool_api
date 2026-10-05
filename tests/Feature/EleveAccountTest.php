<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Emails\NewUserMail;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Pedagogie\Models\Eleve;

uses(RefreshDatabase::class);

function eleveAccountPayload(string $email = 'eleve@example.com'): array
{
    return [
        'nom' => 'Mba',
        'prenom' => 'Kevin',
        'sexe' => 'masculin',
        'date_naissance' => '2012-04-05',
        'lieu_naissance' => 'Libreville',
        'nationalite' => 'Gabonaise',
        'email' => $email,
        'statut' => 'preinscrit',
    ];
}

function eleveAccountAdmin(): User
{
    $role = Role::create(['libelle' => 'Administration']);

    return User::create([
        'nom' => 'Admin',
        'prenom' => 'Test',
        'email' => 'admin.eleves@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);
}

it('creates and emails a student login account when an email is supplied', function () {
    $studentRole = Role::create(['libelle' => 'Élève']);
    $admin = eleveAccountAdmin();
    Mail::fake();

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/eleves', eleveAccountPayload())
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.compte.email', 'eleve@example.com');

    $studentId = $response->json('data.id');
    $userId = $response->json('data.user_id');

    $this->assertDatabaseHas('users', [
        'id' => $userId,
        'email' => 'eleve@example.com',
        'role_id' => $studentRole->id,
    ]);
    $this->assertDatabaseHas('eleves', [
        'id' => $studentId,
        'user_id' => $userId,
        'email' => 'eleve@example.com',
    ]);
    Mail::assertSent(NewUserMail::class, 1);
});

it('links a matching existing student account without creating a duplicate or resending credentials', function () {
    $studentRole = Role::create(['libelle' => 'Élève']);
    $admin = eleveAccountAdmin();
    $user = User::create([
        'nom' => 'Mba',
        'prenom' => 'Kevin',
        'email' => 'eleve@example.com',
        'password' => 'secret',
        'role_id' => $studentRole->id,
    ]);
    Mail::fake();

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/eleves', eleveAccountPayload())
        ->assertOk()
        ->assertJsonPath('data.user_id', $user->id);

    expect(Eleve::findOrFail($response->json('data.id'))->user->is($user))->toBeTrue();
    $this->assertDatabaseCount('users', 2);
    Mail::assertNothingSent();
});

it('does not create a student account when no email is supplied', function () {
    $admin = eleveAccountAdmin();
    $payload = eleveAccountPayload();
    unset($payload['email']);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/eleves', $payload)
        ->assertOk()
        ->assertJsonPath('data.user_id', null)
        ->assertJsonPath('data.compte', null);

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('eleves', [
        'id' => $response->json('data.id'),
        'user_id' => null,
    ]);
});

it('rejects using an email owned by a non-student account', function () {
    $admin = eleveAccountAdmin();
    $role = Role::create(['libelle' => 'Comptable']);
    User::create([
        'nom' => 'Other',
        'prenom' => 'Account',
        'email' => 'eleve@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/eleves', eleveAccountPayload())
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');

    $this->assertDatabaseCount('eleves', 0);
});

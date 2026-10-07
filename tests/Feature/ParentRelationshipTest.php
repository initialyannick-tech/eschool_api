<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Emails\NewUserMail;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Pedagogie\Models\ParentModel;

uses(RefreshDatabase::class);

function parentDossierPayload(string $parentEmail = 'parent@example.com', string $studentName = 'Diallo'): array
{
    return [
        'parent' => [
            'nom' => 'Diallo',
            'prenom' => 'Aminata',
            'telephone' => '+241 01 23 45 67',
            'email' => $parentEmail,
            'relation' => 'mere',
            'responsable_principal' => true,
            'responsable_financier' => false,
        ],
        'eleve' => [
            'nom' => $studentName,
            'prenom' => 'Moussa',
            'sexe' => 'masculin',
            'date_naissance' => '2015-03-10',
            'lieu_naissance' => 'Libreville',
            'nationalite' => 'Gabonaise',
            'email' => 'student.'.$studentName.'@example.com',
            'statut' => 'preinscrit',
        ],
    ];
}

function parentAdmin(): User
{
    $role = Role::create(['libelle' => 'Administration']);

    return User::create([
        'nom' => 'Admin',
        'prenom' => 'Test',
        'email' => 'admin@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);
}

it('creates a parent account and atomically links the first child', function () {
    $parentRole = Role::create(['libelle' => 'Parent']);
    Role::create(['libelle' => 'Élève']);
    $admin = parentAdmin();
    Mail::fake();

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/parents/dossier', parentDossierPayload())
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.parent.nombre_enfants', 1)
        ->assertJsonPath('data.parent.compte.email', 'parent@example.com')
        ->assertJsonPath('data.parent.eleves.0.relation', 'mere');

    $parentId = $response->json('data.parent.id');
    $userId = $response->json('data.parent.user_id');
    $studentId = $response->json('data.eleve.id');

    $this->assertDatabaseHas('parents', [
        'id' => $parentId,
        'user_id' => $userId,
        'email' => 'parent@example.com',
    ]);
    $this->assertDatabaseHas('users', [
        'id' => $userId,
        'role_id' => $parentRole->id,
    ]);
    $this->assertDatabaseHas('eleve_parent', [
        'parent_id' => $parentId,
        'eleve_id' => $studentId,
        'relation' => 'mere',
        'responsable_principal' => true,
    ]);

    Mail::assertSent(NewUserMail::class, 2);
});

it('allows the same parent profile to be linked to additional children', function () {
    $parentRole = Role::create(['libelle' => 'Parent']);
    Role::create(['libelle' => 'Élève']);
    $admin = parentAdmin();
    Mail::fake();

    $firstDossier = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/parents/dossier', parentDossierPayload())
        ->assertOk()
        ->json('data');

    $parentId = $firstDossier['parent']['id'];

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/eleves', [
            'nom' => 'Diallo',
            'prenom' => 'Fatima',
            'sexe' => 'feminin',
            'date_naissance' => '2017-06-14',
            'lieu_naissance' => 'Port-Gentil',
            'statut' => 'preinscrit',
            'parent_id' => $parentId,
            'relation' => 'mere',
            'responsable_principal' => true,
            'responsable_financier' => true,
        ])
        ->assertOk()
        ->assertJsonPath('success', true);

    $parent = ParentModel::with('eleves')->findOrFail($parentId);

    expect($parent->user->role_id)->toBe($parentRole->id);
    expect($parent->eleves)->toHaveCount(2);
    $this->assertDatabaseCount('eleve_parent', 2);
    Mail::assertSent(NewUserMail::class, 2);
});

it('creates a parent and child without a login account when no email is supplied', function () {
    $admin = parentAdmin();
    $payload = parentDossierPayload('', 'Koumba');
    unset($payload['parent']['email']);
    unset($payload['eleve']['email']);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/parents/dossier', $payload)
        ->assertOk()
        ->assertJsonPath('data.parent.user_id', null)
        ->assertJsonPath('data.parent.compte', null)
        ->assertJsonPath('data.parent.nombre_enfants', 1);

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('eleve_parent', 1);
});

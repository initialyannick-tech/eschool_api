<?php

use Modules\Admin\Models\User;
use Modules\Pedagogie\Models\Matiere;

it('peut créer une matière et l\'affecter à plusieurs enseignants', function () {
    $teacherOne = User::create([
        'nom' => 'NDOU',
        'prenom' => 'Alice',
        'email' => 'alice.ndou@example.com',
        'password' => 'azerty',
        'password_changed' => User::ACTIVE,
        'role_id' => User::ENSEIGNANT,
    ]);

    $teacherTwo = User::create([
        'nom' => 'MBOU',
        'prenom' => 'Paul',
        'email' => 'paul.mbou@example.com',
        'password' => 'azerty',
        'password_changed' => User::ACTIVE,
        'role_id' => User::ENSEIGNANT,
    ]);

    $matiere = Matiere::create([
        'code' => 'MATH-01',
        'libelle' => 'Mathématiques',
        'description' => 'Matière fondamentale',
        'actif' => true,
    ]);

    $matiere->enseignants()->attach([$teacherOne->id, $teacherTwo->id]);

    expect($matiere->enseignants()->count())->toBe(2)
        ->and($teacherOne->fresh()->matieres()->where('matieres.id', $matiere->id)->exists())->toBeTrue()
        ->and($teacherTwo->fresh()->matieres()->where('matieres.id', $matiere->id)->exists())->toBeTrue();
});

it('expose un backend enseignant dédié et permet sa suppression', function () {
    $teacher = User::create([
        'nom' => 'DIALLO',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'azerty',
        'password_changed' => User::ACTIVE,
        'role_id' => User::ENSEIGNANT,
    ]);

    $this->actingAs($teacher, 'sanctum')
        ->getJson('/api/enseignants')
        ->assertOk()
        ->assertJsonFragment(['email' => 'mamadou.diallo@example.com']);

    $this->actingAs($teacher, 'sanctum')
        ->deleteJson('/api/enseignants/' . $teacher->id)
        ->assertOk();

    expect(User::find($teacher->id))->toBeNull();
});

it('peut lister les enseignants sans spécialité sans provoquer une erreur serveur', function () {
    $teacher = User::create([
        'nom' => 'KONE',
        'prenom' => 'Sophie',
        'email' => 'sophie.kone@example.com',
        'password' => 'azerty',
        'password_changed' => User::ACTIVE,
        'role_id' => User::ENSEIGNANT,
    ]);

    $this->actingAs($teacher, 'sanctum')
        ->getJson('/api/enseignants')
        ->assertOk()
        ->assertJsonFragment(['email' => 'sophie.kone@example.com']);
});

it('permet de mettre à jour un enseignant sans rejeter son email actuel', function () {
    $teacher = User::create([
        'nom' => 'DIALLO',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'azerty',
        'password_changed' => User::ACTIVE,
        'role_id' => User::ENSEIGNANT,
        'status' => User::ACTIVE,
    ]);

    $this->actingAs($teacher, 'sanctum')
        ->putJson('/api/enseignants/' . $teacher->id, [
            'nom' => 'DIALLO',
            'prenom' => 'Mamadou',
            'email' => 'mamadou.diallo@example.com',
            'role_id' => User::ENSEIGNANT,
            'status' => User::INACTIVE,
        ])
        ->assertOk();

    expect($teacher->fresh()->status)->toBe(User::INACTIVE);
});

it('expose la spécialité et les matières d’un enseignant dans la liste', function () {
    $teacher = User::create([
        'nom' => 'DIOP',
        'prenom' => 'Awa',
        'email' => 'awa.diop@example.com',
        'password' => 'azerty',
        'password_changed' => User::ACTIVE,
        'role_id' => User::ENSEIGNANT,
        'status' => User::ACTIVE,
    ]);

    $specialite = \Modules\Admin\Models\Specialite::create([
        'nom' => 'Mathématiques',
    ]);

    $matiere = Matiere::create([
        'code' => 'MATH-EX',
        'libelle' => 'Mathématiques avancées',
        'description' => 'Matière de test',
        'actif' => true,
    ]);

    $teacher->specialite_id = $specialite->id;
    $teacher->save();
    $teacher->matieres()->sync([$matiere->id]);

    $response = $this->actingAs($teacher, 'sanctum')
        ->getJson('/api/enseignants');

    $response->assertOk()
        ->assertJsonPath('data.0.specialite.nom', 'Mathématiques')
        ->assertJsonPath('data.0.status', 'active')
        ->assertJsonPath('data.0.matiere_count', 1)
        ->assertJsonFragment(['libelle' => 'Mathématiques avancées']);
});

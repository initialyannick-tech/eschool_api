<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Emails\NewUserMail;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;

uses(RefreshDatabase::class);

it('creates an enseignant login account and emails a generated temporary password', function () {
    Role::create(['libelle' => 'Super Administrateur']);
    Role::create(['libelle' => 'Direction']);
    Role::create(['libelle' => 'Administration']);
    $teacherRole = Role::create(['libelle' => 'Enseignant']);
    $admin = User::create([
        'nom' => 'Admin',
        'prenom' => 'Test',
        'email' => 'admin.teacher@example.com',
        'password' => 'secret',
        'role_id' => $teacherRole->id,
    ]);
    Mail::fake();

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson('/api/enseignants', [
            'nom' => 'Mba',
            'prenom' => 'Clarisse',
            'email' => 'clarisse.mba@example.com',
        ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.email', 'clarisse.mba@example.com');

    $teacher = User::where('email', 'clarisse.mba@example.com')->firstOrFail();

    expect($teacher->role_id)->toBe($teacherRole->id)
        ->and(Hash::check('azerty', $teacher->password))->toBeFalse();

    Mail::assertSent(NewUserMail::class, function (NewUserMail $mail) use ($teacher): bool {
        return $mail->user->is($teacher)
            && Hash::check($mail->password, $teacher->password)
            && $mail->password !== 'azerty';
    });
});

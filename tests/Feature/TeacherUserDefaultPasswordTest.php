<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\UserRepository;

test('teacher users get the default password azerty when created from the user module', function () {
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->string('libelle');
        $table->text('description')->nullable();
        $table->timestamps();
    });

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->string('prenom');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('status')->default('active');
        $table->string('password_changed')->nullable();
        $table->unsignedBigInteger('role_id')->nullable();
        $table->timestamps();
    });

    DB::table('roles')->updateOrInsert(
        ['id' => 4],
        ['code' => 'enseignant', 'libelle' => 'Enseignant', 'description' => 'Enseignant', 'created_at' => now(), 'updated_at' => now()]
    );

    $user = app(UserRepository::class)->store([
        'nom' => 'DIALLO',
        'prenom' => 'Mamadou',
        'email' => 'teacher-default-password@test.com',
        'role_id' => 4,
    ]);

    expect($user)->not->toBeFalse();
    expect($user->role_id)->toBe(4);
    expect(Hash::check('azerty', $user->password))->toBeTrue();
});

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Communication\Models\Conversation;
use Modules\Communication\Models\ConversationMessage;
use Modules\Communication\Notifications\CommunicationAnnouncement;
use Modules\Communication\Notifications\InternalMessageReceived;
use Modules\Pedagogie\Models\Eleve;
use Modules\Pedagogie\Models\ParentModel;

uses(RefreshDatabase::class);

it('requires authentication to access the notification inbox', function () {
    $this->getJson('/api/communication/notifications')->assertUnauthorized();
});

it('forbids sending announcements without the management permission', function () {
    $role = Role::create(['libelle' => 'Enseignant']);
    $sender = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);

    Notification::fake();

    $this->actingAs($sender, 'sanctum')
        ->postJson('/api/communication/messages', [
            'title' => 'Réunion',
            'message' => 'La réunion est prévue demain.',
            'audience' => 'all',
            'channels' => ['database'],
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('communication_messages', 0);
    Notification::assertNothingSent();
});

it('sends and records an announcement for active users', function () {
    $role = Role::create(['libelle' => 'Direction']);
    $permission = Permission::create([
        'libelle' => 'Gestion des annonces',
        'code' => 'annonce.management',
    ]);
    $role->permissions()->attach($permission->id);

    $sender = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);
    $recipient = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);

    Notification::fake();

    $this->actingAs($sender, 'sanctum')
        ->postJson('/api/communication/messages', [
            'title' => 'Réunion pédagogique',
            'message' => 'La réunion aura lieu demain à 10 h.',
            'audience' => 'all',
            'channels' => ['database', 'mail'],
        ])
        ->assertOk()
        ->assertJsonPath('data.recipient_count', 2);

    $this->assertDatabaseHas('communication_messages', [
        'titre' => 'Réunion pédagogique',
        'auteur_id' => $sender->id,
        'nombre_destinataires' => 2,
    ]);

    Notification::assertSentTo(
        [$sender, $recipient],
        CommunicationAnnouncement::class,
        fn (CommunicationAnnouncement $notification, array $channels): bool => $channels === ['database', 'mail'],
    );
});

it('sends a role announcement only to active users with that role', function () {
    $managementRole = Role::create(['libelle' => 'Direction']);
    $recipientRole = Role::create(['libelle' => 'Enseignant']);
    $otherRole = Role::create(['libelle' => 'Comptable']);
    $permission = Permission::create([
        'libelle' => 'Gestion des annonces',
        'code' => 'annonce.management',
    ]);
    $managementRole->permissions()->attach($permission->id);

    $sender = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
        'role_id' => $managementRole->id,
    ]);
    $recipient = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $recipientRole->id,
    ]);
    User::create([
        'nom' => 'Kone',
        'prenom' => 'Sophie',
        'email' => 'sophie.kone@example.com',
        'password' => 'secret',
        'role_id' => $otherRole->id,
    ]);

    Notification::fake();

    $this->actingAs($sender, 'sanctum')
        ->postJson('/api/communication/messages', [
            'title' => 'Réunion des enseignants',
            'message' => 'Rendez-vous à 10 h.',
            'audience' => 'role',
            'role_id' => $recipientRole->id,
            'channels' => ['database'],
        ])
        ->assertOk()
        ->assertJsonPath('data.recipient_count', 1);

    Notification::assertSentTo(
        [$recipient],
        CommunicationAnnouncement::class,
        fn (CommunicationAnnouncement $notification, array $channels): bool => $channels === ['database'],
    );
});

it('lists and marks only the signed-in user notifications as read', function () {
    $user = User::create([
        'nom' => 'Kone',
        'prenom' => 'Sophie',
        'email' => 'sophie.kone@example.com',
        'password' => 'secret',
    ]);
    $notification = $user->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => CommunicationAnnouncement::class,
        'data' => [
            'title' => 'Conseil de classe',
            'message' => 'Le conseil commence à 14 h.',
            'sender_name' => 'Direction',
            'channels' => ['database'],
        ],
    ]);
    $otherUser = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
    ]);
    $otherNotification = $otherUser->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => CommunicationAnnouncement::class,
        'data' => [
            'title' => 'Message privé',
            'message' => 'Information réservée.',
            'channels' => ['database'],
        ],
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/communication/notifications')
        ->assertOk()
        ->assertJsonFragment(['title' => 'Conseil de classe'])
        ->assertJsonMissing(['title' => 'Message privé']);

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/communication/notifications/'.$otherNotification->id.'/read')
        ->assertNotFound();
    expect($otherNotification->fresh()->read_at)->toBeNull();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/communication/notifications/'.$notification->id.'/read')
        ->assertOk();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('creates a private conversation and notifies its recipient', function () {
    $sendPermission = Permission::create([
        'libelle' => 'Envoyer un message',
        'code' => 'message.envoyer',
    ]);
    $senderRole = Role::create(['libelle' => 'Enseignant']);
    $senderRole->permissions()->attach($sendPermission->id);
    $sender = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $senderRole->id,
    ]);
    $recipient = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
    ]);

    Notification::fake();

    $this->actingAs($sender, 'sanctum')
        ->postJson('/api/communication/conversations', [
            'recipient_id' => $recipient->id,
            'body' => 'Bonjour, pouvez-vous me rappeler ?',
        ])
        ->assertCreated()
        ->assertJsonPath('data.message.body', 'Bonjour, pouvez-vous me rappeler ?');

    $this->assertDatabaseHas('conversations', ['cree_par_id' => $sender->id]);
    $this->assertDatabaseHas('conversation_messages', [
        'auteur_id' => $sender->id,
        'contenu' => 'Bonjour, pouvez-vous me rappeler ?',
    ]);

    Notification::assertSentTo(
        [$recipient],
        InternalMessageReceived::class,
        fn (InternalMessageReceived $notification, array $channels): bool => $channels === ['database'],
    );
});

it('prevents a user from creating a conversation with themselves', function () {
    $permission = Permission::create([
        'libelle' => 'Envoyer un message',
        'code' => 'message.envoyer',
    ]);
    $role = Role::create(['libelle' => 'Enseignant']);
    $role->permissions()->attach($permission->id);
    $user = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/communication/conversations', [
            'recipient_id' => $user->id,
            'body' => 'Message à soi-même.',
        ])
        ->assertUnprocessable();

    $this->assertDatabaseCount('conversations', 0);
});

it('hides a conversation from authenticated users who are not members', function () {
    $sendPermission = Permission::create([
        'libelle' => 'Envoyer un message',
        'code' => 'message.envoyer',
    ]);
    $readPermission = Permission::create([
        'libelle' => 'Consulter les messages',
        'code' => 'message.consulter',
    ]);
    $senderRole = Role::create(['libelle' => 'Direction']);
    $senderRole->permissions()->attach($sendPermission->id);
    $outsiderRole = Role::create(['libelle' => 'Enseignant']);
    $outsiderRole->permissions()->attach($readPermission->id);
    $sender = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
        'role_id' => $senderRole->id,
    ]);
    $recipientRole = Role::create(['libelle' => 'Parent']);
    $readPermission = Permission::create([
        'libelle' => 'Consulter les messages',
        'code' => 'message.consulter',
    ]);
    $recipientRole->permissions()->attach($readPermission->id);
    $recipient = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $recipientRole->id,
    ]);
    $outsider = User::create([
        'nom' => 'Kone',
        'prenom' => 'Sophie',
        'email' => 'sophie.kone@example.com',
        'password' => 'secret',
        'role_id' => $outsiderRole->id,
    ]);

    Notification::fake();

    $response = $this->actingAs($sender, 'sanctum')
        ->postJson('/api/communication/conversations', [
            'recipient_id' => $recipient->id,
            'body' => 'Information privée.',
        ])
        ->assertCreated();
    $conversationId = $response->json('data.conversation_id');

    $this->actingAs($outsider, 'sanctum')
        ->getJson('/api/communication/conversations/'.$conversationId.'/messages')
        ->assertNotFound();
});

it('marks the conversation notification as read when the recipient opens the thread', function () {
    $readPermission = Permission::create([
        'libelle' => 'Consulter les messages',
        'code' => 'message.consulter',
    ]);
    $role = Role::create(['libelle' => 'Parent']);
    $role->permissions()->attach($readPermission->id);
    $sender = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
    ]);
    $recipient = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);
    $conversation = Conversation::create(['cree_par_id' => $sender->id]);
    $conversation->participants()->attach([$sender->id, $recipient->id]);
    ConversationMessage::create([
        'conversation_id' => $conversation->id,
        'auteur_id' => $sender->id,
        'contenu' => 'Bonjour.',
    ]);
    $notification = $recipient->notifications()->create([
        'id' => (string) Str::uuid(),
        'type' => InternalMessageReceived::class,
        'data' => [
            'type' => 'internal_message',
            'conversation_id' => $conversation->id,
            'title' => 'Nouveau message',
            'message' => 'Bonjour.',
        ],
    ]);

    $this->actingAs($recipient, 'sanctum')
        ->getJson('/api/communication/conversations/'.$conversation->id.'/messages')
        ->assertOk()
        ->assertJsonFragment(['body' => 'Bonjour.']);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('lets a participant reply and archive only their own conversation', function () {
    $sendPermission = Permission::create([
        'libelle' => 'Envoyer un message',
        'code' => 'message.envoyer',
    ]);
    $readPermission = Permission::create([
        'libelle' => 'Consulter les messages',
        'code' => 'message.consulter',
    ]);
    $senderRole = Role::create(['libelle' => 'Parent']);
    $senderRole->permissions()->attach([$sendPermission->id, $readPermission->id]);
    $sender = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
        'role_id' => $senderRole->id,
    ]);
    $recipient = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $senderRole->id,
    ]);
    $conversation = Conversation::create(['cree_par_id' => $sender->id]);
    $conversation->participants()->attach([$sender->id, $recipient->id]);
    Notification::fake();

    $this->actingAs($sender, 'sanctum')
        ->postJson('/api/communication/conversations/'.$conversation->id.'/messages', [
            'body' => 'Voici la réponse.',
        ])
        ->assertOk()
        ->assertJsonPath('data.body', 'Voici la réponse.');

    $this->assertDatabaseHas('conversation_messages', [
        'conversation_id' => $conversation->id,
        'auteur_id' => $sender->id,
        'contenu' => 'Voici la réponse.',
    ]);
    Notification::assertSentTo([$recipient], InternalMessageReceived::class);

    $this->actingAs($sender, 'sanctum')
        ->patchJson('/api/communication/conversations/'.$conversation->id.'/archive', ['archived' => true])
        ->assertOk();

    $this->assertDatabaseHas('conversation_participants', [
        'conversation_id' => $conversation->id,
        'user_id' => $recipient->id,
        'archive_le' => null,
    ]);
    expect($recipient->conversations()->wherePivotNull('archive_le')->count())->toBe(1);

    $this->actingAs($sender, 'sanctum')
        ->getJson('/api/communication/conversations')
        ->assertJsonMissing(['id' => $conversation->id]);
    $this->actingAs($recipient, 'sanctum')
        ->getJson('/api/communication/conversations')
        ->assertJsonFragment(['id' => $conversation->id]);
});

it('stores attachments privately and restricts downloads to conversation members', function () {
    $sendPermission = Permission::create([
        'libelle' => 'Envoyer un message',
        'code' => 'message.envoyer',
    ]);
    $readPermission = Permission::create([
        'libelle' => 'Consulter les messages',
        'code' => 'message.consulter',
    ]);
    $senderRole = Role::create(['libelle' => 'Direction']);
    $senderRole->permissions()->attach($sendPermission->id);
    $outsiderRole = Role::create(['libelle' => 'Enseignant']);
    $outsiderRole->permissions()->attach($readPermission->id);
    $sender = User::create([
        'nom' => 'Barry',
        'prenom' => 'Aminata',
        'email' => 'aminata.barry@example.com',
        'password' => 'secret',
        'role_id' => $senderRole->id,
    ]);
    $recipientRole = Role::create(['libelle' => 'Parent']);
    $recipientRole->permissions()->attach($readPermission->id);
    $recipient = User::create([
        'nom' => 'Diallo',
        'prenom' => 'Mamadou',
        'email' => 'mamadou.diallo@example.com',
        'password' => 'secret',
        'role_id' => $recipientRole->id,
    ]);
    $outsider = User::create([
        'nom' => 'Kone',
        'prenom' => 'Sophie',
        'email' => 'sophie.kone@example.com',
        'password' => 'secret',
        'role_id' => $outsiderRole->id,
    ]);
    Storage::fake('local');
    Notification::fake();

    $response = $this->actingAs($sender, 'sanctum')
        ->post('/api/communication/conversations', [
            'recipient_id' => $recipient->id,
            'body' => '',
            'attachment' => UploadedFile::fake()->create('document.pdf', 20, 'application/pdf'),
        ])
        ->assertCreated();
    $messageId = $response->json('data.message.id');
    $conversationId = $response->json('data.conversation_id');
    $attachmentPath = DB::table('conversation_messages')->where('id', $messageId)->value('piece_jointe_chemin');

    Storage::disk('local')->assertExists($attachmentPath);

    $this->actingAs($recipient, 'sanctum')
        ->get('/api/communication/conversations/'.$conversationId.'/messages/'.$messageId.'/attachment')
        ->assertOk()
        ->assertDownload('document.pdf');

    $this->actingAs($outsider, 'sanctum')
        ->get('/api/communication/conversations/'.$conversationId.'/messages/'.$messageId.'/attachment')
        ->assertNotFound();
});

it('shows a parent the announcements and all linked children in the portal', function () {
    $role = Role::create(['libelle' => 'Parent']);
    $parentUser = User::create([
        'nom' => 'Mabika',
        'prenom' => 'Aline',
        'email' => 'aline.mabika@example.com',
        'password' => 'secret',
        'role_id' => $role->id,
    ]);
    $parent = ParentModel::create([
        'nom' => 'Mabika',
        'prenom' => 'Aline',
        'telephone' => '01234567',
        'email' => 'aline.mabika@example.com',
        'user_id' => $parentUser->id,
    ]);
    $children = collect([
        ['nom' => 'Mabika', 'prenom' => 'Nina'],
        ['nom' => 'Mabika', 'prenom' => 'Noah'],
    ])->map(fn (array $child): Eleve => Eleve::create([
        ...$child,
        'sexe' => 'feminin',
        'date_naissance' => '2015-05-10',
        'lieu_naissance' => 'Libreville',
    ]));
    $parent->eleves()->attach($children->mapWithKeys(fn (Eleve $child): array => [
        $child->id => [
            'relation' => 'Mère',
            'responsable_principal' => true,
            'responsable_financier' => false,
        ],
    ])->all());

    $this->actingAs($parentUser, 'sanctum')
        ->getJson('/api/communication/portal')
        ->assertOk()
        ->assertJsonPath('data.type', 'parent')
        ->assertJsonCount(2, 'data.children')
        ->assertJsonFragment(['prenom' => 'Nina'])
        ->assertJsonFragment(['prenom' => 'Noah']);
});

it('renames legacy communication columns without losing their values', function () {
    Schema::drop('communication_messages');
    Schema::create('communication_messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sender_id');
        $table->string('title', 180);
        $table->text('message');
        $table->string('audience', 20);
        $table->unsignedBigInteger('target_role_id')->nullable();
        $table->json('channels');
        $table->unsignedInteger('recipient_count');
        $table->timestamp('published_at')->nullable();
        $table->unsignedBigInteger('role_id')->nullable();
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
    });

    DB::table('communication_messages')->insert([
        'sender_id' => 12,
        'title' => 'Réunion',
        'message' => 'Réunion demain.',
        'audience' => 'role',
        'target_role_id' => null,
        'channels' => json_encode(['database']),
        'recipient_count' => 3,
        'published_at' => null,
        'role_id' => 4,
        'sent_at' => '2026-11-16 08:00:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration = require base_path('Modules/Communication/database/migrations/2026_11_16_070000_localize_communication_columns.php');
    $migration->up();

    $this->assertDatabaseHas('communication_messages', [
        'auteur_id' => 12,
        'titre' => 'Réunion',
        'contenu' => 'Réunion demain.',
        'role_cible_id' => 4,
        'nombre_destinataires' => 3,
        'date_publication' => '2026-11-16 08:00:00',
    ]);
    expect(Schema::hasColumn('communication_messages', 'sender_id'))->toBeFalse();
    expect(Schema::hasColumn('communication_messages', 'role_id'))->toBeFalse();
    expect(Schema::hasColumn('communication_messages', 'sent_at'))->toBeFalse();
});

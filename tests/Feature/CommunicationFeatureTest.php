<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Communication\Models\Conversation;
use Modules\Communication\Models\ConversationMessage;
use Modules\Communication\Notifications\CommunicationAnnouncement;
use Modules\Communication\Notifications\InternalMessageReceived;

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
        'title' => 'Réunion pédagogique',
        'sender_id' => $sender->id,
        'recipient_count' => 2,
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

    $this->assertDatabaseHas('conversations', ['created_by' => $sender->id]);
    $this->assertDatabaseHas('conversation_messages', [
        'sender_id' => $sender->id,
        'body' => 'Bonjour, pouvez-vous me rappeler ?',
    ]);

    Notification::assertSentTo(
        [$recipient],
        InternalMessageReceived::class,
        fn (InternalMessageReceived $notification, array $channels): bool => $channels === ['database'],
    );
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
    $conversation = Conversation::create(['created_by' => $sender->id]);
    $conversation->participants()->attach([$sender->id, $recipient->id]);
    ConversationMessage::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $sender->id,
        'body' => 'Bonjour.',
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
    $conversation = Conversation::create(['created_by' => $sender->id]);
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
        'sender_id' => $sender->id,
        'body' => 'Voici la réponse.',
    ]);
    Notification::assertSentTo([$recipient], InternalMessageReceived::class);

    $this->actingAs($sender, 'sanctum')
        ->patchJson('/api/communication/conversations/'.$conversation->id.'/archive', ['archived' => true])
        ->assertOk();

    $this->assertDatabaseHas('conversation_participants', [
        'conversation_id' => $conversation->id,
        'user_id' => $recipient->id,
        'archived_at' => null,
    ]);
    expect($recipient->conversations()->wherePivotNull('archived_at')->count())->toBe(1);

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
    $attachmentPath = DB::table('conversation_messages')->where('id', $messageId)->value('attachment_path');

    Storage::disk('local')->assertExists($attachmentPath);

    $this->actingAs($recipient, 'sanctum')
        ->get('/api/communication/conversations/'.$conversationId.'/messages/'.$messageId.'/attachment')
        ->assertOk()
        ->assertDownload('document.pdf');

    $this->actingAs($outsider, 'sanctum')
        ->get('/api/communication/conversations/'.$conversationId.'/messages/'.$messageId.'/attachment')
        ->assertNotFound();
});

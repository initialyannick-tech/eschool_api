<?php

namespace Modules\Communication\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Communication\Models\CommunicationMessage;
use Modules\Communication\Models\Conversation;
use Modules\Communication\Models\ConversationMessage;
use Modules\Communication\Notifications\CommunicationAnnouncement;
use Modules\Communication\Notifications\InternalMessageReceived;
use Modules\Pedagogie\Models\Eleve;
use Modules\Pedagogie\Models\ParentModel;

class CommunicationController
{
    public function messages(Request $request): JsonResponse
    {
        $user = $request->user();
        $canManage = $this->hasPermission($user, 'annonce.management');
        abort_unless($canManage || $this->hasPermission($user, 'annonce.consulter'), 403);

        $messages = CommunicationMessage::query()
            ->with(['sender:id,nom,prenom', 'targetRole:id,libelle'])
            ->whereNotNull('date_publication')
            ->when(! $canManage, function (Builder $query) use ($user): void {
                $query->where(function (Builder $audience) use ($user): void {
                    $audience->where('audience', 'all')
                        ->orWhere(function (Builder $roleAudience) use ($user): void {
                            $roleAudience->where('audience', 'role')
                                ->where('role_cible_id', $user->role_id);
                        });
                });
            })
            ->latest('date_publication')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $messages->getCollection()->map(fn (CommunicationMessage $message): array => $this->announcementData($message)),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function storeMessage(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->hasPermission($user, 'annonce.management'), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:10000'],
            'audience' => ['required', Rule::in(['all', 'role'])],
            'role_id' => ['nullable', 'integer', 'exists:roles,id', 'required_if:audience,role'],
            'target_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => [Rule::in(['database', 'mail'])],
        ]);

        $roleId = $data['target_role_id'] ?? $data['role_id'] ?? null;
        if ($data['audience'] === 'role' && $roleId === null) {
            throw ValidationException::withMessages([
                'role_id' => 'Le rôle destinataire est obligatoire pour cette annonce.',
            ]);
        }

        $recipients = $this->announcementRecipients($data['audience'], $roleId);
        $announcement = DB::transaction(function () use ($data, $roleId, $recipients, $user): CommunicationMessage {
            $announcement = CommunicationMessage::create([
                'auteur_id' => $user->id,
                'titre' => $data['title'],
                'contenu' => $data['message'],
                'audience' => $data['audience'],
                'role_cible_id' => $roleId,
                'canaux' => array_values(array_unique($data['channels'])),
                'nombre_destinataires' => $recipients->count(),
                'date_publication' => now(),
            ]);

            DB::afterCommit(function () use ($recipients, $announcement): void {
                Notification::send($recipients, new CommunicationAnnouncement(
                    $announcement->load('sender:id,nom,prenom'),
                    $announcement->canaux,
                ));
            });

            return $announcement->load('sender:id,nom,prenom', 'targetRole:id,libelle');
        });

        return response()->json([
            'success' => true,
            'message' => 'Annonce publiée avec succès.',
            'data' => $this->announcementData($announcement),
        ]);
    }

    public function showMessage(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $canManage = $this->hasPermission($user, 'annonce.management');
        abort_unless($canManage || $this->hasPermission($user, 'annonce.consulter'), 403);

        $announcement = CommunicationMessage::query()
            ->with(['sender:id,nom,prenom', 'targetRole:id,libelle'])
            ->whereKey($id)
            ->whereNotNull('date_publication')
            ->when(! $canManage, function (Builder $query) use ($user): void {
                $query->where(function (Builder $audience) use ($user): void {
                    $audience->where('audience', 'all')
                        ->orWhere(function (Builder $roleAudience) use ($user): void {
                            $roleAudience->where('audience', 'role')
                                ->where('role_cible_id', $user->role_id);
                        });
                });
            })
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $this->announcementData($announcement)]);
    }

    public function updateMessage(Request $request, int $id): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'annonce.management'), 403);

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:180'],
            'message' => ['sometimes', 'required', 'string', 'max:10000'],
        ]);
        $announcement = CommunicationMessage::query()->findOrFail($id);
        $announcement->update([
            'titre' => $data['title'] ?? $announcement->titre,
            'contenu' => $data['message'] ?? $announcement->contenu,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Annonce modifiée. Les destinataires déjà notifiés ne recevront pas de nouvel e-mail.',
            'data' => $this->announcementData($announcement->fresh('sender:id,nom,prenom')),
        ]);
    }

    public function destroyMessage(Request $request, int $id): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'annonce.management'), 403);

        CommunicationMessage::query()->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Annonce supprimée.']);
    }

    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications->getCollection()->map(fn ($notification): array => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'notification',
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'sender_name' => $notification->data['sender_name'] ?? null,
                'conversation_id' => $notification->data['conversation_id'] ?? null,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ]),
            'unread_count' => $user->unreadNotifications()->count(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function markNotificationRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notification marquée comme lue.']);
    }

    public function contacts(Request $request): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'message.envoyer'), 403);

        $contacts = User::query()
            ->where('status', User::ACTIVE)
            ->where('id', '!=', $request->user()->id)
            ->with('role:id,libelle,code')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get(['id', 'nom', 'prenom', 'email', 'role_id']);

        return response()->json([
            'success' => true,
            'data' => $contacts->map(fn (User $contact): array => [
                'id' => $contact->id,
                'nom' => $contact->nom,
                'prenom' => $contact->prenom,
                'email' => $contact->email,
                'role_id' => $contact->role_id,
                'role' => $contact->role?->libelle,
            ]),
        ]);
    }

    public function roles(Request $request): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'annonce.management'), 403);

        return response()->json([
            'success' => true,
            'data' => Role::query()->orderBy('libelle')->get(['id', 'libelle']),
        ]);
    }

    public function conversations(Request $request): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'message.consulter'), 403);
        $user = $request->user();

        $conversations = $user->conversations()
            ->wherePivotNull('archive_le')
            ->with('participants:id,nom,prenom,email')
            ->orderByDesc('conversation_participants.updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations->map(function (Conversation $conversation) use ($user): array {
                $lastMessage = $conversation->messages()->with('sender:id,nom,prenom')->latest()->first();

                return [
                    'id' => $conversation->id,
                    'participants' => $conversation->participants->map(fn (User $participant): array => [
                        'id' => $participant->id,
                        'nom' => $participant->nom,
                        'prenom' => $participant->prenom,
                    ]),
                    'last_message' => $lastMessage ? $this->conversationMessageData($lastMessage) : null,
                    'unread_count' => $conversation->messages()
                        ->where('auteur_id', '!=', $user->id)
                        ->when($conversation->pivot->derniere_lecture_le, fn (Builder $query, $readAt) => $query->where('created_at', '>', $readAt))
                        ->count(),
                ];
            }),
        ]);
    }

    public function startConversation(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->hasPermission($user, 'message.envoyer'), 403);

        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id', Rule::notIn([$user->id])],
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);
        $this->validateMessageContent($data['body'] ?? null, $request);

        $recipient = User::query()
            ->where('status', User::ACTIVE)
            ->findOrFail($data['recipient_id']);

        [$conversation, $message] = $this->createConversation($request, $user, $recipient, $data['body'] ?? null);
        $recipient->notify(new InternalMessageReceived($message->load('sender:id,nom,prenom')));

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé.',
            'data' => [
                'conversation_id' => $conversation->id,
                'message' => $this->conversationMessageData($message),
            ],
        ], 201);
    }

    public function conversationMessages(Request $request, int $conversationId): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'message.consulter'), 403);
        $user = $request->user();
        $conversation = $this->findParticipantConversation($user, $conversationId);

        $messages = $conversation->messages()->with('sender:id,nom,prenom')->oldest()->get();
        $conversation->participants()->updateExistingPivot($user->id, ['derniere_lecture_le' => now()]);

        $user->notifications()
            ->where('type', InternalMessageReceived::class)
            ->whereNull('read_at')
            ->get()
            ->filter(fn ($notification): bool => (int) ($notification->data['conversation_id'] ?? 0) === $conversation->id)
            ->each->markAsRead();

        return response()->json([
            'success' => true,
            'data' => $messages->map(fn (ConversationMessage $message): array => $this->conversationMessageData($message)),
        ]);
    }

    public function replyToConversation(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->hasPermission($user, 'message.envoyer'), 403);
        $conversation = $this->findParticipantConversation($user, $conversationId);

        $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ]);
        $this->validateMessageContent($request->input('body'), $request);

        [$message] = $this->createReply($request, $conversation, $user);
        $recipients = $conversation->participants()->where('users.id', '!=', $user->id)->get();
        Notification::send($recipients, new InternalMessageReceived($message->load('sender:id,nom,prenom')));

        return response()->json([
            'success' => true,
            'data' => $this->conversationMessageData($message),
        ]);
    }

    public function archiveConversation(Request $request, int $conversationId): JsonResponse
    {
        abort_unless($this->hasPermission($request->user(), 'message.consulter'), 403);
        $data = $request->validate(['archived' => ['required', 'boolean']]);
        $conversation = $this->findParticipantConversation($request->user(), $conversationId);

        $conversation->participants()->updateExistingPivot($request->user()->id, [
            'archive_le' => $data['archived'] ? now() : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Conversation mise à jour.']);
    }

    public function attachment(Request $request, int $conversationId, int $messageId)
    {
        $user = $request->user();
        abort_unless($this->hasPermission($user, 'message.consulter'), 403);
        $conversation = $this->findParticipantConversation($user, $conversationId);
        $message = $conversation->messages()->findOrFail($messageId);
        abort_unless($message->piece_jointe_chemin && Storage::disk('local')->exists($message->piece_jointe_chemin), 404);

        return Storage::disk('local')->download($message->piece_jointe_chemin, $message->piece_jointe_nom);
    }

    public function portal(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('role');
        $roleCode = $user->role?->code;

        if ($roleCode === 'parent') {
            $parent = ParentModel::query()->where('user_id', $user->id)->firstOrFail();
            $children = $parent->eleves()->orderBy('nom')->orderBy('prenom')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'parent',
                    'profile' => [
                        'nom' => $parent->nom,
                        'prenom' => $parent->prenom,
                        'email' => $parent->email,
                    ],
                    'children' => $children->map(fn (Eleve $child): array => $this->studentPortalData($child)),
                    'announcements' => $this->portalAnnouncements($user),
                    'unread_count' => $user->unreadNotifications()->count(),
                ],
            ]);
        }

        if ($roleCode === 'eleve') {
            $student = Eleve::query()->where('user_id', $user->id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'eleve',
                    'profile' => $this->studentPortalData($student),
                    'parents' => $student->parents()->orderBy('nom')->orderBy('prenom')->get()
                        ->map(fn (ParentModel $parent): array => [
                            'id' => $parent->id,
                            'nom' => $parent->nom,
                            'prenom' => $parent->prenom,
                            'telephone' => $parent->telephone,
                            'email' => $parent->email,
                        ]),
                    'announcements' => $this->portalAnnouncements($user),
                    'unread_count' => $user->unreadNotifications()->count(),
                ],
            ]);
        }

        abort(403, 'Cet espace est réservé aux parents et aux élèves.');
    }

    private function announcementRecipients(string $audience, ?int $roleId)
    {
        return User::query()
            ->where('status', User::ACTIVE)
            ->when($audience === 'role', fn (Builder $query) => $query->where('role_id', $roleId))
            ->get();
    }

    private function announcementData(CommunicationMessage $message): array
    {
        return [
            'id' => $message->id,
            'title' => $message->titre,
            'message' => $message->contenu,
            'audience' => $message->audience,
            'target_role_id' => $message->role_cible_id,
            'target_role' => $message->targetRole?->libelle,
            'channels' => $message->canaux,
            'recipient_count' => $message->nombre_destinataires,
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'nom' => $message->sender->nom,
                'prenom' => $message->sender->prenom,
            ] : null,
            'published_at' => $message->date_publication,
        ];
    }

    private function portalAnnouncements(User $user): array
    {
        return CommunicationMessage::query()
            ->with(['sender:id,nom,prenom', 'targetRole:id,libelle'])
            ->whereNotNull('date_publication')
            ->where(function (Builder $query) use ($user): void {
                $query->where('audience', 'all')
                    ->orWhere(function (Builder $roleAudience) use ($user): void {
                        $roleAudience->where('audience', 'role')
                            ->where('role_cible_id', $user->role_id);
                    });
            })
            ->latest('date_publication')
            ->limit(5)
            ->get()
            ->map(fn (CommunicationMessage $message): array => $this->announcementData($message))
            ->all();
    }

    private function studentPortalData(Eleve $student): array
    {
        return [
            'id' => $student->id,
            'matricule' => $student->matricule,
            'nom' => $student->nom,
            'prenom' => $student->prenom,
            'statut' => $student->statut,
            'date_naissance' => $student->date_naissance?->format('Y-m-d'),
        ];
    }

    private function hasPermission(User $user, string $permission): bool
    {
        return $user->role?->permissions()->where('code', $permission)->exists() ?? false;
    }

    private function validateMessageContent(?string $body, Request $request): void
    {
        if (trim((string) $body) === '' && ! $request->hasFile('attachment')) {
            throw ValidationException::withMessages([
                'body' => 'Écrivez un message ou ajoutez une pièce jointe.',
            ]);
        }
    }

    private function createConversation(Request $request, User $sender, User $recipient, ?string $body): array
    {
        $attachmentPath = $request->file('attachment')?->store('conversation-attachments', 'local');
        if ($request->hasFile('attachment') && ! $attachmentPath) {
            throw new \RuntimeException('La pièce jointe n’a pas pu être enregistrée.');
        }

        try {
            return DB::transaction(function () use ($sender, $recipient, $body, $request, $attachmentPath): array {
                $conversation = Conversation::create(['cree_par_id' => $sender->id]);
                $conversation->participants()->attach([$sender->id, $recipient->id]);
                $message = $conversation->messages()->create(
                    $this->messageAttributes($sender, $body, $request, $attachmentPath),
                );

                return [$conversation, $message->load('sender:id,nom,prenom')];
            });
        } catch (\Throwable $exception) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }
            throw $exception;
        }
    }

    private function createReply(Request $request, Conversation $conversation, User $sender): array
    {
        $attachmentPath = $request->file('attachment')?->store('conversation-attachments', 'local');
        if ($request->hasFile('attachment') && ! $attachmentPath) {
            throw new \RuntimeException('La pièce jointe n’a pas pu être enregistrée.');
        }

        try {
            return DB::transaction(function () use ($conversation, $sender, $request, $attachmentPath): array {
                $message = $conversation->messages()->create(
                    $this->messageAttributes($sender, $request->input('body'), $request, $attachmentPath),
                );
                $conversation->participants()->updateExistingPivot($sender->id, [
                    'archive_le' => null,
                    'derniere_lecture_le' => now(),
                ]);

                return [$message->load('sender:id,nom,prenom')];
            });
        } catch (\Throwable $exception) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }
            throw $exception;
        }
    }

    private function messageAttributes(User $sender, ?string $body, Request $request, ?string $attachmentPath): array
    {
        $attachment = $request->file('attachment');

        return [
            'auteur_id' => $sender->id,
            'contenu' => trim((string) $body) !== '' ? trim((string) $body) : null,
            'piece_jointe_chemin' => $attachmentPath,
            'piece_jointe_nom' => $attachment?->getClientOriginalName(),
            'piece_jointe_mime' => $attachment?->getMimeType(),
            'piece_jointe_taille' => $attachment?->getSize(),
        ];
    }

    private function findParticipantConversation(User $user, int $conversationId): Conversation
    {
        return Conversation::query()
            ->whereKey($conversationId)
            ->whereHas('participants', fn (Builder $query) => $query->whereKey($user->id))
            ->firstOrFail();
    }

    private function conversationMessageData(ConversationMessage $message): array
    {
        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->auteur_id,
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'nom' => $message->sender->nom,
                'prenom' => $message->sender->prenom,
            ] : null,
            'body' => $message->contenu,
            'attachment_name' => $message->piece_jointe_nom,
            'attachment_size' => $message->piece_jointe_taille,
            'attachment_url' => $message->piece_jointe_chemin
                ? route('api.communication.conversations.attachment', [$message->conversation_id, $message->id])
                : null,
            'created_at' => $message->created_at,
        ];
    }
}

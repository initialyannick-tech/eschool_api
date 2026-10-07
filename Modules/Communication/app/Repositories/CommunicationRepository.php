<?php

namespace Modules\Communication\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Communication\Models\CommunicationMessage;
use Modules\Communication\Models\Conversation;
use Modules\Communication\Models\ConversationMessage;
use Modules\Communication\Notifications\CommunicationAnnouncement;
use Modules\Communication\Notifications\InternalMessageReceived;

class CommunicationRepository
{
    public function notifications(User $user, bool $unreadOnly = false): LengthAwarePaginator
    {
        $notifications = $unreadOnly ? $user->unreadNotifications() : $user->notifications();

        return $notifications->latest()->paginate(12);
    }

    public function messages(): LengthAwarePaginator
    {
        return CommunicationMessage::query()
            ->with(['sender:id,nom,prenom', 'role:id,libelle'])
            ->latest()
            ->paginate(10);
    }

    public function roles(): Collection
    {
        return Role::query()->orderBy('libelle')->get(['id', 'libelle']);
    }

    public function recipients(User $user, ?string $search = null): Collection
    {
        return User::query()
            ->with('role:id,libelle')
            ->where('status', User::ACTIVE)
            ->where('id', '!=', $user->id)
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('nom', 'like', '%'.$search.'%')
                        ->orWhere('prenom', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->limit(30)
            ->get(['id', 'nom', 'prenom', 'email', 'role_id']);
    }

    public function conversations(User $user, bool $archived = false, ?string $search = null): LengthAwarePaginator
    {
        $conversations = $user->conversations();
        if ($archived) {
            $conversations->wherePivotNotNull('archived_at');
        } else {
            $conversations->wherePivotNull('archived_at');
        }

        $conversations->with([
            'participants:id,nom,prenom,email',
            'latestMessage.sender:id,nom,prenom',
        ])->orderByDesc('conversations.updated_at');

        if ($search !== null && $search !== '') {
            $conversations->where(function ($query) use ($search, $user): void {
                $query->where('conversations.subject', 'like', '%'.$search.'%')
                    ->orWhereHas('participants', function ($participants) use ($search, $user): void {
                        $participants->where('users.id', '!=', $user->id)
                            ->where(function ($details) use ($search): void {
                                $details->where('nom', 'like', '%'.$search.'%')
                                    ->orWhere('prenom', 'like', '%'.$search.'%')
                                    ->orWhere('email', 'like', '%'.$search.'%');
                            });
                    })
                    ->orWhereHas('messages', fn ($messages) => $messages->where('body', 'like', '%'.$search.'%'));
            });
        }

        return $conversations->paginate(15);
    }

    public function startConversation(User $sender, array $data): array
    {
        if ((int) $data['recipient_id'] === (int) $sender->id) {
            throw ValidationException::withMessages([
                'recipient_id' => 'Vous ne pouvez pas démarrer une conversation avec vous-même.',
            ]);
        }

        $recipient = User::query()
            ->where('status', User::ACTIVE)
            ->findOrFail($data['recipient_id']);

        return DB::transaction(function () use ($sender, $recipient, $data): array {
            $conversation = Conversation::create([
                'created_by' => $sender->id,
                'subject' => $data['subject'] ?? null,
            ]);
            $conversation->participants()->attach([$sender->id, $recipient->id]);

            $message = $this->storeMessage($conversation, $sender, $data);

            return [
                'conversation' => $conversation->load(['participants:id,nom,prenom,email', 'latestMessage.sender:id,nom,prenom']),
                'message' => $message->load('sender:id,nom,prenom'),
            ];
        });
    }

    /**
     * @param  array{body?: string|null, attachment?: UploadedFile|null}  $data
     */
    public function reply(User $sender, int $conversationId, array $data): ConversationMessage
    {
        $conversation = $sender->conversations()->whereKey($conversationId)->firstOrFail();

        return DB::transaction(fn () => $this->storeMessage($conversation, $sender, $data)
            ->load('sender:id,nom,prenom'));
    }

    public function conversationMessages(User $user, int $conversationId): LengthAwarePaginator
    {
        $conversation = $user->conversations()->whereKey($conversationId)->firstOrFail();
        $user->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);
        $user->notifications()
            ->where('data->conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $conversation->messages()
            ->with('sender:id,nom,prenom')
            ->latest()
            ->paginate(30);
    }

    public function setArchived(User $user, int $conversationId, bool $archived): void
    {
        $conversation = $user->conversations()->whereKey($conversationId)->firstOrFail();
        $user->conversations()->updateExistingPivot($conversation->id, [
            'archived_at' => $archived ? now() : null,
        ]);
    }

    public function attachment(User $user, int $conversationId, int $messageId): ConversationMessage
    {
        $conversation = $user->conversations()->whereKey($conversationId)->firstOrFail();

        return $conversation->messages()
            ->whereKey($messageId)
            ->whereNotNull('attachment_path')
            ->firstOrFail();
    }

    /**
     * @param  array{body?: string|null, attachment?: UploadedFile|null}  $data
     */
    private function storeMessage(Conversation $conversation, User $sender, array $data): ConversationMessage
    {
        $attachment = $data['attachment'] ?? null;
        $attachmentPath = $attachment?->store('communication/messages', 'local');

        try {
            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'body' => $data['body'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachment?->getClientOriginalName(),
                'attachment_mime' => $attachment?->getMimeType(),
                'attachment_size' => $attachment?->getSize(),
            ]);
            $conversation->touch();

            $recipients = $conversation->participants()
                ->where('users.id', '!=', $sender->id)
                ->get();

            Notification::send($recipients, new InternalMessageReceived(
                $conversation->id,
                $message->id,
                trim($sender->prenom.' '.$sender->nom),
                Str::limit($message->body ?? $message->attachment_name ?? 'Une pièce jointe', 140),
            ));

            return $message;
        } catch (\Throwable $exception) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }

            throw $exception;
        }
    }

    /**
     * @param  array{title: string, message: string, audience: string, role_id?: int|null, channels: array<int, string>}  $data
     * @return array{message: CommunicationMessage, recipient_count: int}
     */
    public function send(User $sender, array $data): array
    {
        $recipients = User::query()->where('status', User::ACTIVE);

        if ($data['audience'] === 'role') {
            $recipients->where('role_id', $data['role_id']);
        }

        $recipientUsers = $recipients->get();

        if ($recipientUsers->isEmpty()) {
            throw ValidationException::withMessages([
                'audience' => 'Aucun utilisateur actif ne correspond à cette cible.',
            ]);
        }

        $communicationMessage = CommunicationMessage::create([
            'sender_id' => $sender->id,
            'role_id' => $data['audience'] === 'role' ? $data['role_id'] : null,
            'title' => $data['title'],
            'message' => $data['message'],
            'audience' => $data['audience'],
            'channels' => $data['channels'],
            'recipient_count' => $recipientUsers->count(),
            'sent_at' => now(),
        ]);

        Notification::send($recipientUsers, new CommunicationAnnouncement(
            $communicationMessage->title,
            $communicationMessage->message,
            $communicationMessage->channels,
            $communicationMessage->id,
            trim($sender->prenom.' '.$sender->nom),
        ));

        return [
            'message' => $communicationMessage->load(['sender:id,nom,prenom', 'role:id,libelle']),
            'recipient_count' => $recipientUsers->count(),
        ];
    }
}

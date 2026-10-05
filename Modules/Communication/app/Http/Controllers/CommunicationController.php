<?php

namespace Modules\Communication\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Models\User;
use Modules\Communication\Http\Requests\ReplyToConversationRequest;
use Modules\Communication\Http\Requests\SendCommunicationRequest;
use Modules\Communication\Http\Requests\SendInternalMessageRequest;
use Modules\Communication\Repositories\CommunicationRepository;
use Modules\Communication\Transformers\CommunicationMessageResource;
use Modules\Communication\Transformers\CommunicationNotificationResource;
use Modules\Communication\Transformers\ConversationMessageResource;
use Modules\Communication\Transformers\ConversationResource;
use Modules\Core\Http\Controllers\CoreController;

class CommunicationController extends CoreController
{
    public function __construct(private CommunicationRepository $repository) {}

    public function notifications(Request $request): JsonResponse
    {
        $unreadOnly = $request->query('filter') === 'unread';
        $notifications = $this->repository->notifications($request->user(), $unreadOnly);

        return $this->returnSuccess(
            'Notifications récupérées',
            CommunicationNotificationResource::collection($notifications),
        );
    }

    public function markRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->markAsRead();

        return $this->returnSuccess('Notification marquée comme lue');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $unread = $request->user()->unreadNotifications();
        $updatedCount = $unread->count();
        $unread->update(['read_at' => now()]);

        return $this->returnSuccess('Notifications marquées comme lues', ['updated_count' => $updatedCount]);
    }

    public function roles(Request $request): JsonResponse
    {
        $this->authorizeManagement($request->user());

        return $this->returnSuccess('Rôles récupérés', $this->repository->roles());
    }

    public function messages(Request $request): JsonResponse
    {
        $this->authorizeManagement($request->user());

        return $this->returnSuccess(
            'Historique des communications récupéré',
            CommunicationMessageResource::collection($this->repository->messages()),
        );
    }

    public function send(SendCommunicationRequest $request): JsonResponse
    {
        $result = $this->repository->send($request->user(), $request->validated());

        return $this->returnSuccess('Communication envoyée', [
            'message' => new CommunicationMessageResource($result['message']),
            'recipient_count' => $result['recipient_count'],
        ]);
    }

    public function recipients(Request $request): JsonResponse
    {
        $this->authorizePermission($request->user(), ['message.envoyer', 'messagerie.management']);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return $this->returnSuccess(
            'Destinataires récupérés',
            $this->repository->recipients($request->user(), $filters['search'] ?? null)->map(
                fn (User $user): array => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'role' => $user->role?->libelle,
                ],
            ),
        );
    }

    public function conversations(Request $request): JsonResponse
    {
        $this->authorizePermission($request->user(), ['message.consulter', 'messagerie.management']);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'archived' => ['sometimes', 'boolean'],
        ]);

        return $this->returnSuccess(
            'Conversations récupérées',
            ConversationResource::collection($this->repository->conversations(
                $request->user(),
                (bool) ($filters['archived'] ?? false),
                $filters['search'] ?? null,
            )),
        );
    }

    public function startConversation(SendInternalMessageRequest $request): JsonResponse
    {
        $result = $this->repository->startConversation($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé',
            'data' => [
                'conversation_id' => $result['conversation']->id,
                'conversation' => new ConversationResource($result['conversation']),
                'message' => new ConversationMessageResource($result['message']),
            ],
        ], 201);
    }

    public function conversationMessages(Request $request, int $conversationId): JsonResponse
    {
        $this->authorizePermission($request->user(), ['message.consulter', 'messagerie.management']);

        return $this->returnSuccess(
            'Messages récupérés',
            ConversationMessageResource::collection($this->repository->conversationMessages($request->user(), $conversationId)),
        );
    }

    public function reply(ReplyToConversationRequest $request, int $conversationId): JsonResponse
    {
        $message = $this->repository->reply($request->user(), $conversationId, $request->validated());

        return $this->returnSuccess('Réponse envoyée', new ConversationMessageResource($message));
    }

    public function archive(Request $request, int $conversationId): JsonResponse
    {
        $this->authorizePermission($request->user(), ['message.consulter', 'messagerie.management']);
        $data = $request->validate(['archived' => ['required', 'boolean']]);
        $this->repository->setArchived($request->user(), $conversationId, $data['archived']);

        return $this->returnSuccess($data['archived'] ? 'Conversation archivée' : 'Conversation restaurée');
    }

    public function attachment(Request $request, int $conversationId, int $messageId)
    {
        $this->authorizePermission($request->user(), ['message.consulter', 'messagerie.management']);
        $message = $this->repository->attachment($request->user(), $conversationId, $messageId);

        abort_unless(Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::download($message->attachment_path, $message->attachment_name, [
            'Content-Type' => $message->attachment_mime,
        ]);
    }

    private function authorizeManagement(User $user): void
    {
        $this->authorizePermission($user, ['annonce.management']);
    }

    /**
     * @param  array<int, string>  $permissions
     */
    private function authorizePermission(User $user, array $permissions): void
    {
        abort_unless($user->role?->permissions()->whereIn('code', $permissions)->exists(), 403);
    }
}

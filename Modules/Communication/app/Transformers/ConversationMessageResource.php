<?php

namespace Modules\Communication\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender' => $this->sender?->only('id', 'nom', 'prenom'),
            'body' => $this->body,
            'attachment' => $this->attachment_path ? [
                'name' => $this->attachment_name,
                'mime' => $this->attachment_mime,
                'size' => $this->attachment_size,
                'url' => route('api.communication.conversations.attachments.show', [
                    'conversationId' => $this->conversation_id,
                    'messageId' => $this->id,
                ]),
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}

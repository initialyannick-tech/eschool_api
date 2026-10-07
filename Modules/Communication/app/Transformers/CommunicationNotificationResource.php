<?php

namespace Modules\Communication\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->data['title'] ?? 'Notification',
            'message' => $this->data['message'] ?? '',
            'sender_name' => $this->data['sender_name'] ?? null,
            'type' => $this->data['type'] ?? null,
            'conversation_id' => $this->data['conversation_id'] ?? null,
            'channels' => $this->data['channels'] ?? [],
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}

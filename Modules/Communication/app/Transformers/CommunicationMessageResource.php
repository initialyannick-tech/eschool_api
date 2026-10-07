<?php

namespace Modules\Communication\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'audience' => $this->audience,
            'role' => $this->role?->only('id', 'libelle'),
            'channels' => $this->channels,
            'recipient_count' => $this->recipient_count,
            'sender' => $this->sender?->only('id', 'nom', 'prenom'),
            'sent_at' => $this->sent_at,
        ];
    }
}

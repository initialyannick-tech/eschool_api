<?php

namespace Modules\Communication\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $participant = $this->participants->firstWhere('id', $request->user()->id);
        $lastReadAt = $participant?->pivot?->last_read_at;
        $unreadCount = $this->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->when($lastReadAt, fn ($query) => $query->where('created_at', '>', $lastReadAt))
            ->count();

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'participants' => $this->participants->map(fn ($user) => $user->only('id', 'nom', 'prenom', 'email')),
            'last_message' => $this->whenLoaded('latestMessage', fn () => new ConversationMessageResource($this->latestMessage)),
            'unread_count' => $unreadCount,
            'archived_at' => $participant?->pivot?->archived_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace Modules\Communication\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class InternalMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $conversationId,
        public int $messageId,
        public string $senderName,
        public string $preview,
    ) {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'internal_message',
            'conversation_id' => $this->conversationId,
            'message_id' => $this->messageId,
            'title' => 'Nouveau message de '.$this->senderName,
            'message' => $this->preview,
            'sender_name' => $this->senderName,
            'channels' => ['database'],
        ];
    }
}

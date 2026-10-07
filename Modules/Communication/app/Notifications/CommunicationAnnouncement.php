<?php

namespace Modules\Communication\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunicationAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public array $channels,
        public int $messageId,
        public string $senderName,
    ) {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Bonjour '.$notifiable->prenom)
            ->line($this->message)
            ->salutation($this->senderName.' - '.config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->messageId,
            'title' => $this->title,
            'message' => $this->message,
            'sender_name' => $this->senderName,
            'channels' => $this->channels,
        ];
    }
}

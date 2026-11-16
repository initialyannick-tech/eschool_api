<?php

namespace Modules\Communication\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Communication\Models\CommunicationMessage;

class CommunicationAnnouncement extends Notification
{
    use Queueable;

    public function __construct(
        private readonly CommunicationMessage $announcement,
        private readonly array $channels,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'announcement',
            'communication_message_id' => $this->announcement->id,
            'title' => $this->announcement->titre,
            'message' => $this->announcement->contenu,
            'sender_name' => trim($this->announcement->sender->prenom.' '.$this->announcement->sender->nom),
            'channels' => $this->channels,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->announcement->titre)
            ->greeting('Bonjour '.$notifiable->prenom.' '.$notifiable->nom.',')
            ->line($this->announcement->contenu)
            ->salutation('L’équipe eSchool');
    }
}

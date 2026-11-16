<?php

namespace Modules\Communication\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Communication\Models\ConversationMessage;

class InternalMessageReceived extends Notification
{
    use Queueable;

    public function __construct(
        private readonly ConversationMessage $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'internal_message',
            'conversation_id' => $this->message->conversation_id,
            'message_id' => $this->message->id,
            'title' => 'Nouveau message',
            'message' => $this->message->body ?: 'Une pièce jointe vous a été envoyée.',
            'sender_name' => trim($this->message->sender->prenom.' '.$this->message->sender->nom),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Vous avez reçu un nouveau message sur eSchool')
            ->greeting('Bonjour '.$notifiable->prenom.' '.$notifiable->nom.',')
            ->line($this->message->body ?: 'Une pièce jointe vous a été envoyée.')
            ->salutation('L’équipe eSchool');
    }
}

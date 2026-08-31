<?php

namespace Modules\Admin\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Admin\Models\User;

class NewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;



    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;

    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Bienvenue sur la plateforme')
            ->markdown('emails.users.new');
    }
}

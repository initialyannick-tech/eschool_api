<?php

namespace Modules\Core\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Admin\Models\User;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }


    /**
     * Build the message.
     */
    public function build(): WelcomeMail
    {
        return $this->markdown('core::emails.welcome')
            ->subject('Bienvenue sur ' . config('app.name'))
            ->with('user', $this->user)
            ->with('password', $this->password);
    }
}

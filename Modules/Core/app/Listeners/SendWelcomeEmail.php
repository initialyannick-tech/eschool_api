<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\Core\Emails\WelcomeMail;
use Modules\Core\Events\UserCreated;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeMail($event->user, $event->password));
    }
}

<?php

namespace Modules\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Admin\Models\User;

class UserCreated
{
    use Dispatchable, SerializesModels;

    public $user;
    public $password;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
}

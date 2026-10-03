<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    Modules\Core\Providers\CoreServiceProvider::class,
    Modules\Admin\Providers\AdminServiceProvider::class,
    Modules\Parametre\Providers\ParametreServiceProvider::class,
    Modules\Pedagogie\Providers\PedagogieServiceProvider::class,
];

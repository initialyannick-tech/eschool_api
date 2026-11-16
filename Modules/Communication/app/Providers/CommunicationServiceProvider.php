<?php

namespace Modules\Communication\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CommunicationServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Communication';

    protected string $nameLower = 'communication';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
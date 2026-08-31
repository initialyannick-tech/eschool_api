<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeControllerRepository extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:controller-repository {name} {module}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a controller and repository within a specific module from stubs';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $module = $this->argument('module');

        $this->createController($name, $module);
        $this->createRepository($name, $module);

        $this->info('Le Controller et la Repository ont été crées avec succès dans le module : ' . $module);
    }

    /**
     * Create a controller from the stub.
     */
    protected function createController($name, $module): void
    {
        $controllerDir = base_path("Modules/{$module}/App/Http/Controllers/");
        $controllerPath = $controllerDir . "{$name}Controller.php";
        $stubPath = base_path('Modules/Core/resources/stubs/controller.stub');

        // Vérifier et créer le répertoire s'il n'existe pas
        if (!File::exists($controllerDir)) {
            File::makeDirectory($controllerDir, 0755, true); // Crée le répertoire avec des permissions adéquates
        }

        if (!File::exists($controllerPath)) {
            $stubContent = File::get($stubPath);

            $controllerContent = str_replace(
                [
                    '{{ namespace }}',
                    '{{ className }}',
                    '{{ repositoryNamespace }}', '{{ repositoryClass }}'],
                [
                    "Modules\\{$module}\\App\\Http\\Controllers",
                    "{$name}Controller",
                    "Modules\\{$module}\\Repositories\\{$name}Repository",
                    "{$name}Repository"
                ],
                $stubContent
            );

            File::put($controllerPath, $controllerContent);
            $this->info("Controller {$name}Controller created successfully in module {$module}.");
        } else {
            $this->warn("Controller {$name}Controller already exists in module {$module}.");
        }
    }


    /**
     * Create a repository from the stub.
     */
    protected function createRepository($name, $module): void
    {
        $repositoryDir = base_path("Modules/{$module}/Repositories/");
        $repositoryPath = $repositoryDir . "{$name}Repository.php";
        $stubPath = base_path('Modules/Core/resources/stubs/repository.stub');

        // Vérifier et créer le répertoire s'il n'existe pas
        if (!File::exists($repositoryDir)) {
            File::makeDirectory($repositoryDir, 0755, true);
        }

        if (!File::exists($repositoryPath)) {
            $stubContent = File::get($stubPath);

            $repositoryContent = str_replace(
                [
                    '{{ namespace }}',
                    '{{ className }}'
                ],
                [
                    "Modules\\{$module}\\Repositories",
                    "{$name}Repository"
                ],
                $stubContent
            );

            File::put($repositoryPath, $repositoryContent);
            $this->info("Repository {$name}Repository created successfully in module {$module}.");
        } else {
            $this->warn("Repository {$name}Repository already exists in module {$module}.");
        }
    }
}

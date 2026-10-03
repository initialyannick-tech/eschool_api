<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Modules\Admin\Database\Seeders\AdminDatabaseSeeder::class,
            \Modules\Parametre\Database\Seeders\ParametreDatabaseSeeder::class,
            \Modules\Pedagogie\Database\Seeders\PedagogieDatabaseSeeder::class,
        ]);
    }
}

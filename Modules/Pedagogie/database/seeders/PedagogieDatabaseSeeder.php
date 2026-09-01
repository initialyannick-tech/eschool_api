<?php

namespace Modules\Pedagogie\Database\Seeders;

use Illuminate\Database\Seeder;

class PedagogieDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->call([
             CycleSeeder::class,
             SerieSeeder::class,
         ]);
    }
}

<?php

namespace Modules\Parametre\Database\Seeders;

use Illuminate\Database\Seeder;

class ParametreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->call([
             AnneeScolaireSeeder::class,
         ]);
    }
}

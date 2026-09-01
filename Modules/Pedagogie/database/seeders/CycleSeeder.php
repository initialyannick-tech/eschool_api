<?php

namespace Modules\Pedagogie\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pedagogie\Models\Cycle;

class CycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cycle::updateOrCreate(
            [
                'code' => 'premier'
            ],
            [
                'libelle' => 'Premier cycle',
                'description' => 'De la 6e à la 3e',
                'actif' => true,
            ]
        );

        Cycle::updateOrCreate(
            [
                'code' => 'second'
            ],
            [
                'libelle' => 'Second cycle',
                'description' => 'De la 2nde à la Tle',
                'actif' => true,
            ]
        );
    }
}

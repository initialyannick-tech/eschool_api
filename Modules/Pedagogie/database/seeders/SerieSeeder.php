<?php

namespace Modules\Pedagogie\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pedagogie\Models\Serie;

class SerieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Serie::updateOrCreate(
            [
                'code' => 'S'
            ],
            [
                'libelle' => 'Scientifique',
                'description' => 'Série scientifique',
                'actif' => true,
            ]
        );

        Serie::updateOrCreate(
            [
                'code' => 'LE'
            ],
            [
                'libelle' => 'Littéraire',
                'description' => 'Série littéraire',
                'actif' => true,
            ]
        );
        Serie::updateOrCreate(
            [
                'code' => 'B'
            ],
            [
                'libelle' => 'Science Economique',
                'description' => 'Série littéraire & science économique',
                'actif' => true,
            ]
        );
        Serie::updateOrCreate(
            [
                'code' => 'A1'
            ],
            [
                'libelle' => 'Littéraire (Sémi scientique)',
                'description' => 'Série littéraire & sicentifique',
                'actif' => true,
            ]
        );
        Serie::updateOrCreate(
            [
                'code' => 'A2'
            ],
            [
                'libelle' => 'Littéraire',
                'description' => 'Série littéraire',
                'actif' => true,
            ]
        );
        Serie::updateOrCreate(
            [
                'code' => 'C'
            ],
            [
                'libelle' => 'Mathématique',
                'description' => 'Série mathematique',
                'actif' => true,
            ]
        );
        Serie::updateOrCreate(
            [
                'code' => 'D'
            ],
            [
                'libelle' => 'Sciences',
                'description' => 'Série sciences',
                'actif' => true,
            ]
        );
    }
}

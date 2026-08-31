<?php

namespace Modules\Parametre\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Parametre\Models\AnneeScolaire;

class AnneeScolaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AnneeScolaire::updateOrCreate(
            [
                'libelle' => '2025-2026',
            ],
            [
                'annee_debut' => 2025,
                'annee_fin' => 2026,
                'statut' => 'cloturee',
                'active' => false,
                'date_ouverture' => '2025-09-01',
                'date_cloture' => '2026-07-31',
                'description' => 'Année scolaire 2025-2026',
            ]
        );

        AnneeScolaire::updateOrCreate(
            [
                'libelle' => '2026-2027',
            ],
            [
                'annee_debut' => 2026,
                'annee_fin' => 2027,
                'statut' => 'ouverte',
                'active' => true,
                'date_ouverture' => '2026-09-01',
                'date_cloture' => null,
                'description' => 'Année scolaire 2026-2027',
            ]
        );

        AnneeScolaire::updateOrCreate(
            [
                'libelle' => '2027-2028',
            ],
            [
                'annee_debut' => 2027,
                'annee_fin' => 2028,
                'statut' => 'preparee',
                'active' => false,
                'date_ouverture' => null,
                'date_cloture' => null,
                'description' => 'Année scolaire 2027-2028',
            ]
        );
    }
}

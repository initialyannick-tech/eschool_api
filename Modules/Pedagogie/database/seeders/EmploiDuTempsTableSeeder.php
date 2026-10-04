<?php

namespace Modules\Pedagogie\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Pedagogie\app\Models\EmploiDuTemps;
use Modules\Pedagogie\app\Models\Salle;

class EmploiDuTempsTableSeeder extends Seeder
{
    public function run(): void
    {
        $salleLabo = Salle::where('code', 'LAB_INFO_01')->first();
        $salle101 = Salle::where('code', 'S101')->first();

        $seances = [
            [
                'annee_scolaire_id' => 1,
                'classe_id'         => 1,
                'matiere_id'        => 1,
                'enseignant_id'     => 1,
                'salle_id'          => $salle101?->id,
                'jour_semaine'      => 'lundi',
                'heure_debut'       => '08:00:00',
                'heure_fin'         => '10:00:00',
                'type_cours'        => 'CM',
                'actif'             => true,
            ],
            [
                'annee_scolaire_id' => 1,
                'classe_id'         => 1,
                'matiere_id'        => 2,
                'enseignant_id'     => 2,
                'salle_id'          => $salleLabo?->id,
                'jour_semaine'      => 'lundi',
                'heure_debut'       => '10:15:00',
                'heure_fin'         => '12:15:00',
                'type_cours'        => 'TP',
                'actif'             => true,
            ],
            [
                'annee_scolaire_id' => 1,
                'classe_id'         => 1,
                'matiere_id'        => 1,
                'enseignant_id'     => 1,
                'salle_id'          => $salle101?->id,
                'jour_semaine'      => 'mardi',
                'heure_debut'       => '08:00:00',
                'heure_fin'         => '10:00:00',
                'type_cours'        => 'TD',
                'actif'             => true,
            ],
        ];

        foreach ($seances as $seance) {
            EmploiDuTemps::create($seance);
        }
    }
}
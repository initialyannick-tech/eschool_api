<?php

namespace Modules\Pedagogie\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pedagogie\Models\Matiere;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $matieres = [
            ['code' => 'MATH', 'libelle' => 'Mathématiques', 'description' => 'Matière fondamentale', 'actif' => true],
            ['code' => 'FRAN', 'libelle' => 'Français', 'description' => 'Langue et littérature', 'actif' => true],
            ['code' => 'ANGL', 'libelle' => 'Anglais', 'description' => 'Langue vivante', 'actif' => true],
            ['code' => 'SVT', 'libelle' => 'Sciences de la vie et de la terre', 'description' => 'Biologie et environnement', 'actif' => true],
            ['code' => 'PHYS', 'libelle' => 'Physique', 'description' => 'Sciences physiques', 'actif' => true],
            ['code' => 'HIST', 'libelle' => 'Histoire-Géographie', 'description' => 'Sciences humaines', 'actif' => true],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(
                ['code' => $matiere['code']],
                $matiere
            );
        }
    }
}

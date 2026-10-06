<?php

namespace Modules\Pedagogie\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Pedagogie\Models\Salle;

class SalleTableSeeder extends Seeder
{
    public function run(): void
    {
        $salles = [
            [
                'code' => 'S101',
                'nom' => 'Salle 101 (Bâtiment A)',
                'capacite' => 35,
                'actif' => true,
            ],
            [
                'code' => 'S102',
                'nom' => 'Salle 102 (Bâtiment A)',
                'capacite' => 40,
                'actif' => true,
            ],
            [
                'code' => 'LAB_INFO_01',
                'nom' => 'Laboratoire Informatique 1',
                'capacite' => 25,
                'actif' => true,
            ],
            [
                'code' => 'AMPHI_A',
                'nom' => 'Grand Amphithéâtre A',
                'capacite' => 120,
                'actif' => true,
            ],
        ];

        foreach ($salles as $salle) {
            Salle::updateOrCreate(['code' => $salle['code']], $salle);
        }
    }
}
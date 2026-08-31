<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Models\Specialite;
use Modules\Admin\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = array(
            [
                "nom" => "BIBANG",
                "prenom" => "Joseph",
                "email" => "joseph@eschool.ga",
                "password" => "azerty",
                "password_changed" => "active",
                "role_id" => 1, // Super Administrateur
            ],
            [
                "nom" => "EDOU",
                "prenom" => "Yannick",
                "email" => "direction@eschool.ga",
                "password" => "azerty",
                "password_changed" => "active",
                "role_id" => 2, // Direction
            ],
            [
                "nom" => "MINKO",
                "prenom" => "Brigitte",
                "email" => "administration@eschool.ga",
                "password" => "azerty",
                "password_changed" => "active",
                "role_id" => 3, // Administration
            ],
            [
                "nom" => "OKA",
                "prenom" => "Jacques",
                "email" => "comptable@eschool.ga",
                "password" => "azerty",
                "password_changed" => "active",
                "role_id" => 5, // Comptable
            ],
            [
                "nom" => "MBOUROU",
                "prenom" => "Paul",
                "email" => "parent@eschool.ga",
                "password" => "azerty",
                "password_changed" => "active",
                "role_id" => 6, // Parent / Tuteur
            ],
            [
                "nom" => "OBAME",
                "prenom" => "Kevin",
                "email" => "eleve@eschool.ga",
                "password" => "azerty",
                "password_changed" => "active",
                "role_id" => 7, // Élève
            ],
        );

        foreach ($users as $user) {
            User::create($user); // Cela déclenchera l'événement `creating`
        }

        /*
        |--------------------------------------------------------------------------
        | ENSEIGNANTS PAR SPÉCIALITÉ
        |--------------------------------------------------------------------------
        */

        $specialities = Specialite::all();
        $teachers = [
            'Mathématique' => [
                ['nom' => 'Ngoua', 'prenom' => 'Pierre'],
                ['nom' => 'Mboumba', 'prenom' => 'Alice'],
            ],

            'Physique' => [
                ['nom' => 'Zue', 'prenom' => 'Patricia'],
                ['nom' => 'Okinda', 'prenom' => 'Louis'],
            ],

            'SVT' => [
                ['nom' => 'Engo', 'prenom' => 'Claude'],
                ['nom' => 'Bouka', 'prenom' => 'Carine'],
            ],

            'Histoire & Géographie' => [
                ['nom' => 'Obame', 'prenom' => 'Jean'],
                ['nom' => 'Meye', 'prenom' => 'Sophie'],
            ],

            'Science Economique' => [
                ['nom' => 'Mba', 'prenom' => 'Patrick'],
                ['nom' => 'Ndong', 'prenom' => 'Carine'],
            ],

            'Anglais' => [
                ['nom' => 'Minko', 'prenom' => 'Sarah'],
                ['nom' => 'Ella', 'prenom' => 'David'],
            ],

            'Espagnol' => [
                ['nom' => 'Okoro', 'prenom' => 'Carlos'],
                ['nom' => 'Mounguengui', 'prenom' => 'Maria'],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | CRÉATION DES ENSEIGNANTS
        |--------------------------------------------------------------------------
        */

        foreach ($specialities as $speciality) {
            if (isset($teachers[$speciality->nom])) {
                foreach ($teachers[$speciality->nom] as $teacher) {
                    User::create([
                        "nom" => $teacher['nom'],
                        "prenom" => $teacher['prenom'],
                        "email" => strtolower($teacher['nom'])
                            . "."
                            . strtolower($teacher['prenom'])
                            . "@eschool.ga",
                        "password" => "azerty",
                        "password_changed" => "active",
                        "role_id" => 4, // Enseignant
                        "specialite_id" => $speciality->id,
                    ]);
                }
            }
        }
    }
}

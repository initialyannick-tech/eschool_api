<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SPÉCIALITÉS
        |--------------------------------------------------------------------------
        */
        $specialities = [
            [
                'nom' => 'Mathématique',
                'description' => 'Spécialité consacrée à l’enseignement des mathématiques.'
            ],
            [
                'nom' => 'Physique',
                'description' => 'Spécialité consacrée à l’enseignement de la physique.'
            ],
            [
                'nom' => 'SVT',
                'description' => 'Spécialité consacrée aux sciences de la vie et de la Terre.'
            ],
            [
                'nom' => 'Histoire & Géographie',
                'description' => 'Spécialité consacrée à l’enseignement de l’histoire et de la géographie.'
            ],
            [
                'nom' => 'Science Economique',
                'description' => 'Spécialité consacrée à l’enseignement des sciences économiques.'
            ],
            [
                'nom' => 'Anglais',
                'description' => 'Spécialité consacrée à l’enseignement de la langue anglaise.'
            ],
            [
                'nom' => 'Espagnol',
                'description' => 'Spécialité consacrée à l’enseignement de la langue espagnole.'
            ],
        ];
        DB::table('specialites')->insert($specialities);
        /*
        |--------------------------------------------------------------------------
        | RÔLES
        |--------------------------------------------------------------------------
        */
        $roles = array(
            [
                "libelle" => "Super Administrateur",
                "code" => "super_admin",
                "description" => "Ceci est le rôle du super administrateur avec un accès complet à la plateforme."
            ],
            [
                "libelle" => "Direction",
                "code" => "direction",
                "description" => "Ce rôle est destiné à la direction de l'établissement pour superviser les activités scolaires, administratives et financières."
            ],
            [
                "libelle" => "Administration",
                "code" => "administration",
                "description" => "Ce rôle est destiné au personnel administratif pour la gestion des élèves, parents, inscriptions, classes et dossiers scolaires."
            ],
            [
                "libelle" => "Enseignant",
                "code" => "enseignant",
                "description" => "Ce rôle est destiné aux enseignants pour la gestion des classes, matières, évaluations, notes, absences et cahiers de textes."
            ],
            [
                "libelle" => "Comptable",
                "code" => "comptable",
                "description" => "Ce rôle est destiné au personnel comptable pour la gestion des frais, factures, paiements, reçus, dépenses et opérations financières."
            ],
            [
                "libelle" => "Parent / Tuteur",
                "code" => "parent",
                "description" => "Ce rôle est destiné aux parents et tuteurs pour consulter les informations scolaires et financières de leurs enfants."
            ],
            [
                "libelle" => "Élève",
                "code" => "eleve",
                "description" => "Ce rôle est destiné aux élèves pour consulter leurs informations scolaires, notes, résultats, absences et emploi du temps."
            ],
        );
        DB::table('roles')->insert($roles);
        /*
        |--------------------------------------------------------------------------
        | RÉCUPÉRATION DES RÔLES
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::where('code', 'super_admin')->first();
        $direction = Role::where('code', 'direction')->first();
        $administration = Role::where('code', 'administration')->first();
        $enseignant = Role::where('code', 'enseignant')->first();
        $comptable = Role::where('code', 'comptable')->first();
        $parent = Role::where('code', 'parent')->first();
        $eleve = Role::where('code', 'eleve')->first();

        /*
        |--------------------------------------------------------------------------
        | RÉCUPÉRATION DES PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $allPermissions = Permission::all();

        // Élèves
        $elevePermission = Permission::where('code', 'eleve.management')->first();
        $eleveConsulterPermission = Permission::where('code', 'eleve.consulter')->first();

        // Parents
        $parentPermission = Permission::where('code', 'parent.management')->first();
        $parentConsulterPermission = Permission::where('code', 'parent.consulter')->first();

        // Classes
        $classePermission = Permission::where('code', 'classe.management')->first();
        $classeConsulterPermission = Permission::where('code', 'classe.consulter')->first();

        // Enseignants
        $enseignantPermission = Permission::where('code', 'enseignant.management')->first();

        // Personnel
        $personnelPermission = Permission::where('code', 'personnel.management')->first();

        // Matières
        $matierePermission = Permission::where('code', 'matiere.management')->first();
        $matiereConsulterPermission = Permission::where('code', 'matiere.consulter')->first();

        // Notes
        $notePermission = Permission::where('code', 'note.management')->first();
        $noteSaisirPermission = Permission::where('code', 'note.saisir')->first();
        $noteConsulterPermission = Permission::where('code', 'note.consulter')->first();
        $noteValiderPermission = Permission::where('code', 'note.valider')->first();

        // Évaluations
        $evaluationPermission = Permission::where('code', 'evaluation.management')->first();

        // Absences
        $absencePermission = Permission::where('code', 'absence.management')->first();
        $absenceSaisirPermission = Permission::where('code', 'absence.saisir')->first();
        $absenceConsulterPermission = Permission::where('code', 'absence.consulter')->first();

        // Retards
        $retardPermission = Permission::where('code', 'retard.management')->first();
        $retardConsulterPermission = Permission::where('code', 'retard.consulter')->first();

        // Bulletins
        $bulletinPermission = Permission::where('code', 'bulletin.management')->first();
        $bulletinConsulterPermission = Permission::where('code', 'bulletin.consulter')->first();
        $bulletinGenererPermission = Permission::where('code', 'bulletin.generer')->first();
        $bulletinValiderPermission = Permission::where('code', 'bulletin.valider')->first();

        // Emploi du temps
        $emploiTempsPermission = Permission::where('code', 'emploi_temps.consulter')->first();

        // Cahier de textes
        $cahierTextePermission = Permission::where('code', 'cahier_texte.management')->first();

        // Examens
        $examenPermission = Permission::where('code', 'examen.management')->first();
        $examenConsulterPermission = Permission::where('code', 'examen.consulter')->first();
        $resultatExamenPermission = Permission::where('code', 'resultat_examen.management')->first();
        $resultatExamenConsulterPermission = Permission::where('code', 'resultat_examen.consulter')->first();

        // Inscriptions
        $inscriptionPermission = Permission::where('code', 'inscription.management')->first();
        $preinscriptionPermission = Permission::where('code', 'preinscription.management')->first();
        $reinscriptionPermission = Permission::where('code', 'reinscription.management')->first();

        // Dossiers
        $dossierPermission = Permission::where('code', 'dossier_scolaire.management')->first();
        $dossierConsulterPermission = Permission::where('code', 'dossier_scolaire.consulter')->first();

        // Documents
        $documentPermission = Permission::where('code', 'document_eleve.management')->first();
        $documentConsulterPermission = Permission::where('code', 'document_eleve.consulter')->first();

        // Cycles / niveaux / séries / filières
        $cyclePermission = Permission::where('code', 'cycle.management')->first();
        $niveauPermission = Permission::where('code', 'niveau.management')->first();
        $seriePermission = Permission::where('code', 'serie.management')->first();
        $filierePermission = Permission::where('code', 'filiere.management')->first();

        // Carte scolaire
        $carteScolairePermission = Permission::where('code', 'carte_scolaire.management')->first();

        // Finance
        $fraisTypePermission = Permission::where('code', 'frais_type.management')->first();
        $tarifPermission = Permission::where('code', 'tarif.management')->first();
        $facturePermission = Permission::where('code', 'facture.management')->first();
        $factureConsulterPermission = Permission::where('code', 'facture.consulter')->first();
        $paiementPermission = Permission::where('code', 'paiement.management')->first();
        $paiementConsulterPermission = Permission::where('code', 'paiement.consulter')->first();
        $recuPermission = Permission::where('code', 'recu.management')->first();
        $depensePermission = Permission::where('code', 'depense.management')->first();
        $boursePermission = Permission::where('code', 'bourse.management')->first();
        $vacationPermission = Permission::where('code', 'vacation.management')->first();
        $salairePermission = Permission::where('code', 'salaire.management')->first();

        // Communication
        $messageriePermission = Permission::where('code', 'messagerie.management')->first();
        $messageEnvoyerPermission = Permission::where('code', 'message.envoyer')->first();
        $messageConsulterPermission = Permission::where('code', 'message.consulter')->first();
        $notificationPermission = Permission::where('code', 'notification.management')->first();
        $annoncePermission = Permission::where('code', 'annonce.management')->first();
        $annonceConsulterPermission = Permission::where('code', 'annonce.consulter')->first();

        // Reporting
        $dashboardPermission = Permission::where('code', 'dashboard.consulter')->first();
        $statistiquePermission = Permission::where('code', 'statistique.consulter')->first();
        $rapportPermission = Permission::where('code', 'rapport.consulter')->first();
        $rapportExporterPermission = Permission::where('code', 'rapport.exporter')->first();

        /*
        |--------------------------------------------------------------------------
        | ATTRIBUTION DES PERMISSIONS
        |--------------------------------------------------------------------------
        */

        // Super Administrateur
        $superAdmin->permissions()->attach(
            $allPermissions->pluck('id')
        );

        // Direction
        $direction->permissions()->attach([
            $elevePermission->id,
            $eleveConsulterPermission->id,
            $parentPermission->id,
            $parentConsulterPermission->id,
            $classePermission->id,
            $classeConsulterPermission->id,
            $enseignantPermission->id,
            $personnelPermission->id,
            $matierePermission->id,
            $matiereConsulterPermission->id,
            $notePermission->id,
            $noteConsulterPermission->id,
            $noteValiderPermission->id,
            $bulletinPermission->id,
            $bulletinConsulterPermission->id,
            $bulletinGenererPermission->id,
            $bulletinValiderPermission->id,
            $absencePermission->id,
            $absenceConsulterPermission->id,
            $retardPermission->id,
            $retardConsulterPermission->id,
            $examenPermission->id,
            $resultatExamenPermission->id,
            $facturePermission->id,
            $factureConsulterPermission->id,
            $paiementPermission->id,
            $paiementConsulterPermission->id,
            $recuPermission->id,
            $depensePermission->id,
            $boursePermission->id,
            $messageriePermission->id,
            $messageConsulterPermission->id,
            $notificationPermission->id,
            $annoncePermission->id,
            $annonceConsulterPermission->id,
            $dashboardPermission->id,
            $statistiquePermission->id,
            $rapportPermission->id,
            $rapportExporterPermission->id,
        ]);

        // Administration
        $administration->permissions()->attach([
            $elevePermission->id,
            $eleveConsulterPermission->id,
            $parentPermission->id,
            $parentConsulterPermission->id,
            $classePermission->id,
            $classeConsulterPermission->id,
            $inscriptionPermission->id,
            $preinscriptionPermission->id,
            $reinscriptionPermission->id,
            $dossierPermission->id,
            $dossierConsulterPermission->id,
            $documentPermission->id,
            $documentConsulterPermission->id,
            $cyclePermission->id,
            $niveauPermission->id,
            $seriePermission->id,
            $filierePermission->id,
            $personnelPermission->id,
            $carteScolairePermission->id,
            $dashboardPermission->id,
            $statistiquePermission->id,
            $rapportPermission->id,
            $rapportExporterPermission->id,
        ]);

        // Enseignant
        $enseignant->permissions()->attach([
            $classeConsulterPermission->id,
            $matiereConsulterPermission->id,
            $emploiTempsPermission->id,
            $cahierTextePermission->id,
            $evaluationPermission->id,
            $notePermission->id,
            $noteSaisirPermission->id,
            $noteConsulterPermission->id,
            $absencePermission->id,
            $absenceSaisirPermission->id,
            $absenceConsulterPermission->id,
            $retardPermission->id,
            $retardConsulterPermission->id,
            $bulletinConsulterPermission->id,
            $examenConsulterPermission->id,
            $resultatExamenConsulterPermission->id,
            $messageriePermission->id,
            $messageEnvoyerPermission->id,
            $messageConsulterPermission->id,
            $notificationPermission->id,
            $annonceConsulterPermission->id,
        ]);

        // Comptable
        $comptable->permissions()->attach([
            $fraisTypePermission->id,
            $tarifPermission->id,
            $facturePermission->id,
            $factureConsulterPermission->id,
            $paiementPermission->id,
            $paiementConsulterPermission->id,
            $recuPermission->id,
            $depensePermission->id,
            $boursePermission->id,
            $vacationPermission->id,
            $salairePermission->id,
            $dashboardPermission->id,
            $statistiquePermission->id,
            $rapportPermission->id,
            $rapportExporterPermission->id,
        ]);

        // Parent / Tuteur
        $parent->permissions()->attach([
            $eleveConsulterPermission->id,
            $dossierConsulterPermission->id,
            $documentConsulterPermission->id,
            $classeConsulterPermission->id,
            $matiereConsulterPermission->id,
            $noteConsulterPermission->id,
            $bulletinConsulterPermission->id,
            $absenceConsulterPermission->id,
            $retardConsulterPermission->id,
            $factureConsulterPermission->id,
            $paiementConsulterPermission->id,
            $recuPermission->id,
            $notificationPermission->id,
            $messageriePermission->id,
            $messageEnvoyerPermission->id,
            $messageConsulterPermission->id,
            $annonceConsulterPermission->id,
        ]);

        // Élève
        $eleve->permissions()->attach([
            $eleveConsulterPermission->id,
            $classeConsulterPermission->id,
            $matiereConsulterPermission->id,
            $emploiTempsPermission->id,
            $noteConsulterPermission->id,
            $bulletinConsulterPermission->id,
            $absenceConsulterPermission->id,
            $retardConsulterPermission->id,
            $examenConsulterPermission->id,
            $resultatExamenConsulterPermission->id,
            $notificationPermission->id,
            $annonceConsulterPermission->id,
            $messageriePermission->id,
            $messageEnvoyerPermission->id,
            $messageConsulterPermission->id,
        ]);
    }
}

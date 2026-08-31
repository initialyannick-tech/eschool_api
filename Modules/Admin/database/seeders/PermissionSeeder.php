<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array(

            /*
            |--------------------------------------------------------------------------
            | UTILISATEURS / SÉCURITÉ
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des utilisateurs",
                "code" => "user.management",
            ],
            [
                "libelle" => "Consulter les utilisateurs",
                "code" => "user.consulter",
            ],
            [
                "libelle" => "Créer un utilisateur",
                "code" => "user.creer",
            ],
            [
                "libelle" => "Modifier un utilisateur",
                "code" => "user.modifier",
            ],
            [
                "libelle" => "Supprimer un utilisateur",
                "code" => "user.supprimer",
            ],

            [
                "libelle" => "Gestion des rôles",
                "code" => "role.management",
            ],
            [
                "libelle" => "Gestion des permissions",
                "code" => "permission.management",
            ],

            [
                "libelle" => "Consultation du journal d'activité",
                "code" => "audit.consulter",
            ],

            /*
            |--------------------------------------------------------------------------
            | PARAMÉTRAGE
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des paramètres",
                "code" => "setting.management",
            ],

            [
                "libelle" => "Gestion de l'établissement",
                "code" => "etablissement.management",
            ],

            [
                "libelle" => "Gestion des années scolaires",
                "code" => "annee_scolaire.management",
            ],
            [
                "libelle" => "Consulter les années scolaires",
                "code" => "annee_scolaire.consulter",
            ],
            [
                "libelle" => "Créer une année scolaire",
                "code" => "annee_scolaire.creer",
            ],
            [
                "libelle" => "Modifier une année scolaire",
                "code" => "annee_scolaire.modifier",
            ],
            [
                "libelle" => "Activer une année scolaire",
                "code" => "annee_scolaire.activer",
            ],
            [
                "libelle" => "Clôturer une année scolaire",
                "code" => "annee_scolaire.cloturer",
            ],

            /*
            |--------------------------------------------------------------------------
            | ÉLÈVES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des élèves",
                "code" => "eleve.management",
            ],
            [
                "libelle" => "Consulter les élèves",
                "code" => "eleve.consulter",
            ],
            [
                "libelle" => "Créer un élève",
                "code" => "eleve.creer",
            ],
            [
                "libelle" => "Modifier un élève",
                "code" => "eleve.modifier",
            ],
            [
                "libelle" => "Supprimer un élève",
                "code" => "eleve.supprimer",
            ],

            [
                "libelle" => "Gestion du dossier scolaire",
                "code" => "dossier_scolaire.management",
            ],
            [
                "libelle" => "Consulter le dossier scolaire",
                "code" => "dossier_scolaire.consulter",
            ],
            [
                "libelle" => "Modifier le dossier scolaire",
                "code" => "dossier_scolaire.modifier",
            ],

            [
                "libelle" => "Gestion des documents élèves",
                "code" => "document_eleve.management",
            ],
            [
                "libelle" => "Importer un document élève",
                "code" => "document_eleve.creer",
            ],
            [
                "libelle" => "Consulter les documents élèves",
                "code" => "document_eleve.consulter",
            ],
            [
                "libelle" => "Supprimer un document élève",
                "code" => "document_eleve.supprimer",
            ],

            [
                "libelle" => "Gestion des cartes scolaires",
                "code" => "carte_scolaire.management",
            ],
            [
                "libelle" => "Générer une carte scolaire",
                "code" => "carte_scolaire.generer",
            ],

            /*
            |--------------------------------------------------------------------------
            | PARENTS / TUTEURS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des parents",
                "code" => "parent.management",
            ],
            [
                "libelle" => "Consulter les parents",
                "code" => "parent.consulter",
            ],
            [
                "libelle" => "Créer un parent",
                "code" => "parent.creer",
            ],
            [
                "libelle" => "Modifier un parent",
                "code" => "parent.modifier",
            ],
            [
                "libelle" => "Supprimer un parent",
                "code" => "parent.supprimer",
            ],

            [
                "libelle" => "Gestion des responsables/tuteurs",
                "code" => "tuteur.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | INSCRIPTIONS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des préinscriptions",
                "code" => "preinscription.management",
            ],
            [
                "libelle" => "Créer une préinscription",
                "code" => "preinscription.creer",
            ],
            [
                "libelle" => "Consulter les préinscriptions",
                "code" => "preinscription.consulter",
            ],
            [
                "libelle" => "Modifier une préinscription",
                "code" => "preinscription.modifier",
            ],
            [
                "libelle" => "Valider une préinscription",
                "code" => "preinscription.valider",
            ],
            [
                "libelle" => "Rejeter une préinscription",
                "code" => "preinscription.rejeter",
            ],

            [
                "libelle" => "Gestion des inscriptions",
                "code" => "inscription.management",
            ],
            [
                "libelle" => "Créer une inscription",
                "code" => "inscription.creer",
            ],
            [
                "libelle" => "Consulter les inscriptions",
                "code" => "inscription.consulter",
            ],
            [
                "libelle" => "Modifier une inscription",
                "code" => "inscription.modifier",
            ],
            [
                "libelle" => "Valider une inscription",
                "code" => "inscription.valider",
            ],

            [
                "libelle" => "Gestion des réinscriptions",
                "code" => "reinscription.management",
            ],
            [
                "libelle" => "Créer une réinscription",
                "code" => "reinscription.creer",
            ],
            [
                "libelle" => "Valider une réinscription",
                "code" => "reinscription.valider",
            ],

            /*
            |--------------------------------------------------------------------------
            | CLASSES / STRUCTURE SCOLAIRE
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des cycles",
                "code" => "cycle.management",
            ],

            [
                "libelle" => "Gestion des niveaux",
                "code" => "niveau.management",
            ],

            [
                "libelle" => "Gestion des séries",
                "code" => "serie.management",
            ],

            [
                "libelle" => "Gestion des filières",
                "code" => "filiere.management",
            ],

            [
                "libelle" => "Gestion des classes",
                "code" => "classe.management",
            ],
            [
                "libelle" => "Consulter les classes",
                "code" => "classe.consulter",
            ],
            [
                "libelle" => "Créer une classe",
                "code" => "classe.creer",
            ],
            [
                "libelle" => "Modifier une classe",
                "code" => "classe.modifier",
            ],
            [
                "libelle" => "Supprimer une classe",
                "code" => "classe.supprimer",
            ],
            [
                "libelle" => "Affecter des élèves à une classe",
                "code" => "classe.affecter_eleve",
            ],

            [
                "libelle" => "Gestion des salles",
                "code" => "salle.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | PERSONNEL
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion du personnel",
                "code" => "personnel.management",
            ],
            [
                "libelle" => "Consulter le personnel",
                "code" => "personnel.consulter",
            ],
            [
                "libelle" => "Créer un membre du personnel",
                "code" => "personnel.creer",
            ],
            [
                "libelle" => "Modifier un membre du personnel",
                "code" => "personnel.modifier",
            ],
            [
                "libelle" => "Supprimer un membre du personnel",
                "code" => "personnel.supprimer",
            ],

            [
                "libelle" => "Gestion des enseignants",
                "code" => "enseignant.management",
            ],

            [
                "libelle" => "Gestion du personnel administratif",
                "code" => "personnel_administratif.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | MATIÈRES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des matières",
                "code" => "matiere.management",
            ],
            [
                "libelle" => "Consulter les matières",
                "code" => "matiere.consulter",
            ],
            [
                "libelle" => "Créer une matière",
                "code" => "matiere.creer",
            ],
            [
                "libelle" => "Modifier une matière",
                "code" => "matiere.modifier",
            ],
            [
                "libelle" => "Supprimer une matière",
                "code" => "matiere.supprimer",
            ],

            /*
            |--------------------------------------------------------------------------
            | AFFECTATION DES ENSEIGNANTS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des affectations enseignants",
                "code" => "affectation_enseignant.management",
            ],
            [
                "libelle" => "Affecter un enseignant à une matière",
                "code" => "affectation_enseignant.matiere",
            ],
            [
                "libelle" => "Affecter un enseignant à une classe",
                "code" => "affectation_enseignant.classe",
            ],

            /*
            |--------------------------------------------------------------------------
            | EMPLOI DU TEMPS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des emplois du temps",
                "code" => "emploi_temps.management",
            ],
            [
                "libelle" => "Consulter les emplois du temps",
                "code" => "emploi_temps.consulter",
            ],
            [
                "libelle" => "Créer un emploi du temps",
                "code" => "emploi_temps.creer",
            ],
            [
                "libelle" => "Modifier un emploi du temps",
                "code" => "emploi_temps.modifier",
            ],
            [
                "libelle" => "Supprimer un emploi du temps",
                "code" => "emploi_temps.supprimer",
            ],

            /*
            |--------------------------------------------------------------------------
            | CAHIER DE TEXTES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion du cahier de textes",
                "code" => "cahier_texte.management",
            ],
            [
                "libelle" => "Consulter le cahier de textes",
                "code" => "cahier_texte.consulter",
            ],
            [
                "libelle" => "Créer une séance",
                "code" => "cahier_texte.creer",
            ],
            [
                "libelle" => "Modifier une séance",
                "code" => "cahier_texte.modifier",
            ],

            /*
            |--------------------------------------------------------------------------
            | ÉVALUATIONS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des évaluations",
                "code" => "evaluation.management",
            ],
            [
                "libelle" => "Consulter les évaluations",
                "code" => "evaluation.consulter",
            ],
            [
                "libelle" => "Créer une évaluation",
                "code" => "evaluation.creer",
            ],
            [
                "libelle" => "Modifier une évaluation",
                "code" => "evaluation.modifier",
            ],
            [
                "libelle" => "Supprimer une évaluation",
                "code" => "evaluation.supprimer",
            ],

            /*
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des notes",
                "code" => "note.management",
            ],
            [
                "libelle" => "Consulter les notes",
                "code" => "note.consulter",
            ],
            [
                "libelle" => "Saisir les notes",
                "code" => "note.saisir",
            ],
            [
                "libelle" => "Modifier les notes",
                "code" => "note.modifier",
            ],
            [
                "libelle" => "Valider les notes",
                "code" => "note.valider",
            ],

            /*
            |--------------------------------------------------------------------------
            | BULLETINS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des bulletins",
                "code" => "bulletin.management",
            ],
            [
                "libelle" => "Consulter les bulletins",
                "code" => "bulletin.consulter",
            ],
            [
                "libelle" => "Générer les bulletins",
                "code" => "bulletin.generer",
            ],
            [
                "libelle" => "Valider les bulletins",
                "code" => "bulletin.valider",
            ],
            [
                "libelle" => "Exporter les bulletins",
                "code" => "bulletin.exporter",
            ],

            /*
            |--------------------------------------------------------------------------
            | ABSENCES / RETARDS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des absences",
                "code" => "absence.management",
            ],
            [
                "libelle" => "Consulter les absences",
                "code" => "absence.consulter",
            ],
            [
                "libelle" => "Saisir les absences",
                "code" => "absence.saisir",
            ],
            [
                "libelle" => "Modifier les absences",
                "code" => "absence.modifier",
            ],
            [
                "libelle" => "Justifier une absence",
                "code" => "absence.justifier",
            ],

            [
                "libelle" => "Gestion des retards",
                "code" => "retard.management",
            ],
            [
                "libelle" => "Consulter les retards",
                "code" => "retard.consulter",
            ],
            [
                "libelle" => "Enregistrer un retard",
                "code" => "retard.creer",
            ],

            /*
            |--------------------------------------------------------------------------
            | EXAMENS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des examens",
                "code" => "examen.management",
            ],
            [
                "libelle" => "Gestion des sessions d'examens",
                "code" => "examen_session.management",
            ],
            [
                "libelle" => "Gestion des candidats",
                "code" => "examen_candidat.management",
            ],
            [
                "libelle" => "Gestion des salles d'examen",
                "code" => "examen_salle.management",
            ],
            [
                "libelle" => "Gestion des surveillants",
                "code" => "surveillant.management",
            ],
            [
                "libelle" => "Gestion des résultats d'examen",
                "code" => "resultat_examen.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | FINANCE — FRAIS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des types de frais",
                "code" => "frais_type.management",
            ],

            [
                "libelle" => "Gestion des tarifs",
                "code" => "tarif.management",
            ],

            [
                "libelle" => "Gestion des plans tarifaires",
                "code" => "plan_tarifaire.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | FACTURATION
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des factures",
                "code" => "facture.management",
            ],
            [
                "libelle" => "Consulter les factures",
                "code" => "facture.consulter",
            ],
            [
                "libelle" => "Créer une facture",
                "code" => "facture.creer",
            ],
            [
                "libelle" => "Modifier une facture",
                "code" => "facture.modifier",
            ],
            [
                "libelle" => "Annuler une facture",
                "code" => "facture.annuler",
            ],

            /*
            |--------------------------------------------------------------------------
            | ÉCHÉANCIERS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des échéanciers",
                "code" => "echeancier.management",
            ],
            [
                "libelle" => "Créer un échéancier",
                "code" => "echeancier.creer",
            ],
            [
                "libelle" => "Modifier un échéancier",
                "code" => "echeancier.modifier",
            ],

            /*
            |--------------------------------------------------------------------------
            | PAIEMENTS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des paiements",
                "code" => "paiement.management",
            ],
            [
                "libelle" => "Consulter les paiements",
                "code" => "paiement.consulter",
            ],
            [
                "libelle" => "Enregistrer un paiement",
                "code" => "paiement.enregistrer",
            ],
            [
                "libelle" => "Modifier un paiement",
                "code" => "paiement.modifier",
            ],
            [
                "libelle" => "Annuler un paiement",
                "code" => "paiement.annuler",
            ],

            /*
            |--------------------------------------------------------------------------
            | REÇUS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des reçus",
                "code" => "recu.management",
            ],
            [
                "libelle" => "Générer un reçu",
                "code" => "recu.generer",
            ],
            [
                "libelle" => "Consulter les reçus",
                "code" => "recu.consulter",
            ],

            /*
            |--------------------------------------------------------------------------
            | DÉPENSES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des dépenses",
                "code" => "depense.management",
            ],
            [
                "libelle" => "Consulter les dépenses",
                "code" => "depense.consulter",
            ],
            [
                "libelle" => "Créer une dépense",
                "code" => "depense.creer",
            ],
            [
                "libelle" => "Modifier une dépense",
                "code" => "depense.modifier",
            ],
            [
                "libelle" => "Supprimer une dépense",
                "code" => "depense.supprimer",
            ],

            [
                "libelle" => "Gestion des catégories de dépenses",
                "code" => "categorie_depense.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | BOURSES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des bourses",
                "code" => "bourse.management",
            ],
            [
                "libelle" => "Définir les critères de bourse",
                "code" => "bourse.criteres",
            ],
            [
                "libelle" => "Identifier les élèves éligibles",
                "code" => "bourse.eligibilite",
            ],
            [
                "libelle" => "Valider une bourse",
                "code" => "bourse.valider",
            ],
            [
                "libelle" => "Consulter les bénéficiaires",
                "code" => "bourse.beneficiaires",
            ],

            /*
            |--------------------------------------------------------------------------
            | VACATIONS
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des vacations",
                "code" => "vacation.management",
            ],
            [
                "libelle" => "Consulter les vacations",
                "code" => "vacation.consulter",
            ],
            [
                "libelle" => "Créer une vacation",
                "code" => "vacation.creer",
            ],
            [
                "libelle" => "Modifier une vacation",
                "code" => "vacation.modifier",
            ],
            [
                "libelle" => "Valider une vacation",
                "code" => "vacation.valider",
            ],

            /*
            |--------------------------------------------------------------------------
            | SALAIRES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion des salaires",
                "code" => "salaire.management",
            ],
            [
                "libelle" => "Consulter les salaires",
                "code" => "salaire.consulter",
            ],
            [
                "libelle" => "Gérer les primes",
                "code" => "salaire.prime",
            ],
            [
                "libelle" => "Gérer les retenues",
                "code" => "salaire.retenue",
            ],
            [
                "libelle" => "Valider les salaires",
                "code" => "salaire.valider",
            ],

            /*
            |--------------------------------------------------------------------------
            | COMMUNICATION
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion de la messagerie",
                "code" => "messagerie.management",
            ],
            [
                "libelle" => "Envoyer un message",
                "code" => "message.envoyer",
            ],
            [
                "libelle" => "Consulter les messages",
                "code" => "message.consulter",
            ],

            [
                "libelle" => "Gestion des notifications",
                "code" => "notification.management",
            ],

            [
                "libelle" => "Gestion des annonces",
                "code" => "annonce.management",
            ],
            [
                "libelle" => "Créer une annonce",
                "code" => "annonce.creer",
            ],
            [
                "libelle" => "Modifier une annonce",
                "code" => "annonce.modifier",
            ],
            [
                "libelle" => "Publier une annonce",
                "code" => "annonce.publier",
            ],
            [
                "libelle" => "Supprimer une annonce",
                "code" => "annonce.supprimer",
            ],

            /*
            |--------------------------------------------------------------------------
            | ESPACES PARENTS / ÉLÈVES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Accès à l'espace parent",
                "code" => "espace_parent.consulter",
            ],

            [
                "libelle" => "Accès à l'espace élève",
                "code" => "espace_eleve.consulter",
            ],

            /*
            |--------------------------------------------------------------------------
            | ORIENTATION
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion de l'orientation",
                "code" => "orientation.management",
            ],
            [
                "libelle" => "Orientation des élèves de 5e",
                "code" => "orientation.5e",
            ],
            [
                "libelle" => "Orientation des élèves de 3e",
                "code" => "orientation.3e",
            ],
            [
                "libelle" => "Orientation des élèves de terminale",
                "code" => "orientation.terminale",
            ],
            [
                "libelle" => "Gestion des réorientations",
                "code" => "reorientation.management",
            ],
            [
                "libelle" => "Valider une orientation",
                "code" => "orientation.valider",
            ],

            /*
            |--------------------------------------------------------------------------
            | DISCIPLINE
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Gestion disciplinaire",
                "code" => "discipline.management",
            ],
            [
                "libelle" => "Gestion des incidents",
                "code" => "incident.management",
            ],
            [
                "libelle" => "Gestion des observations",
                "code" => "observation.management",
            ],
            [
                "libelle" => "Gestion des avertissements",
                "code" => "avertissement.management",
            ],
            [
                "libelle" => "Gestion des sanctions",
                "code" => "sanction.management",
            ],
            [
                "libelle" => "Gestion des convocations",
                "code" => "convocation.management",
            ],

            /*
            |--------------------------------------------------------------------------
            | REPORTING / STATISTIQUES
            |--------------------------------------------------------------------------
            */

            [
                "libelle" => "Accès au tableau de bord",
                "code" => "dashboard.consulter",
            ],

            [
                "libelle" => "Consultation des statistiques",
                "code" => "statistique.consulter",
            ],

            [
                "libelle" => "Consultation des statistiques scolaires",
                "code" => "statistique.scolaire",
            ],

            [
                "libelle" => "Consultation des statistiques financières",
                "code" => "statistique.financiere",
            ],

            [
                "libelle" => "Génération des rapports",
                "code" => "rapport.consulter",
            ],

            [
                "libelle" => "Exporter les rapports",
                "code" => "rapport.exporter",
            ],

            [
                "libelle" => "Exporter les données en PDF",
                "code" => "export.pdf",
            ],

            [
                "libelle" => "Exporter les données en Excel",
                "code" => "export.excel",
            ],

            [
                "libelle" => "Exporter les données en CSV",
                "code" => "export.csv",
            ],
            [
                "libelle" => "Gestion des annonces",
                "code" => "annonce.management",
            ],
            [
                "libelle" => "Consulter les annonces",
                "code" => "annonce.consulter",
            ],
            [
                "libelle" => "Gestion des examens",
                "code" => "examen.management",
            ],
            [
                "libelle" => "Consulter les examens",
                "code" => "examen.consulter",
            ],
            [
                "libelle" => "Gestion des résultats d'examens",
                "code" => "resultat_examen.management",
            ],
            [
                "libelle" => "Consulter les résultats d'examens",
                "code" => "resultat_examen.consulter",
            ],

        );

        foreach ($permissions as $permission) {
            Permission::create($permission); // Cela déclenchera l'événement `creating`
        }

    }
}

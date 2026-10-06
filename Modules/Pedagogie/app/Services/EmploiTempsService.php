<?php

namespace Modules\Pedagogie\Services;

use Modules\Pedagogie\Models\EmploiDuTemps;

class EmploiTempsService
{
    /**
     * Détecte les conflits d'horaires pour un cours.
     */
    public function detecterConflits(array $data, ?int $ignoreId = null): array
    {
        $conflits = [];

        // 1. Conflit Enseignant
        $conflitEnseignant = EmploiDuTemps::where('annee_scolaire_id', $data['annee_scolaire_id'])
            ->where('enseignant_id', $data['enseignant_id'])
            ->where('jour_semaine', $data['jour_semaine'])
            ->where('heure_debut', '<', $data['heure_fin'])
            ->where('heure_fin', '>', $data['heure_debut'])
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->first();

        if ($conflitEnseignant) {
            $conflits[] = "L'enseignant a déjà un cours programmé sur ce créneau horaire.";
        }

        // 2. Conflit Classe
        $conflitClasse = EmploiDuTemps::where('annee_scolaire_id', $data['annee_scolaire_id'])
            ->where('classe_id', $data['classe_id'])
            ->where('jour_semaine', $data['jour_semaine'])
            ->where('heure_debut', '<', $data['heure_fin'])
            ->where('heure_fin', '>', $data['heure_debut'])
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->first();

        if ($conflitClasse) {
            $conflits[] = "La classe a déjà un cours prévu sur cette plage horaire.";
        }

        // 3. Conflit Salle
        if (!empty($data['salle_id'])) {
            $conflitSalle = EmploiDuTemps::where('annee_scolaire_id', $data['annee_scolaire_id'])
                ->where('salle_id', $data['salle_id'])
                ->where('jour_semaine', $data['jour_semaine'])
                ->where('heure_debut', '<', $data['heure_fin'])
                ->where('heure_fin', '>', $data['heure_debut'])
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->first();

            if ($conflitSalle) {
                $conflits[] = "La salle sélectionnée est déjà occupée sur cette plage horaire.";
            }
        }

        return $conflits;
    }
}
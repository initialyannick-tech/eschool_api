<?php

namespace Modules\Pedagogie\Repositories;

use Modules\Pedagogie\Models\EmploiDuTemps;
use Illuminate\Database\Eloquent\Collection;

class EmploiDuTempsRepository
{
    public function getByClasse(int $classeId, int $anneeScolaireId): Collection
    {
        return EmploiDuTemps::with(['classe', 'matiere', 'enseignant', 'salle'])
            ->where('classe_id', $classeId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();
    }

    public function getByEnseignant(int $enseignantId, int $anneeScolaireId): Collection
    {
        return EmploiDuTemps::with(['classe', 'matiere', 'enseignant', 'salle'])
            ->where('enseignant_id', $enseignantId)
            ->where('annee_scolaire_id', $anneeScolaireId)
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();
    }

    public function findById(int $id): ?EmploiDuTemps
    {
        return EmploiDuTemps::with(['classe', 'matiere', 'enseignant', 'salle'])->find($id);
    }

    public function create(array $data): EmploiDuTemps
    {
        return EmploiDuTemps::create($data);
    }

    public function update(EmploiDuTemps $emploi, array $data): bool
    {
        return $emploi->update($data);
    }

    public function delete(EmploiDuTemps $emploi): bool
    {
        return $emploi->delete();
    }
}
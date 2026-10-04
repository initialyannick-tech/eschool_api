<?php

namespace Modules\Pedagogie\Repositories;

use Modules\Admin\Models\User;
use Modules\Pedagogie\Models\Matiere;

class AffectationEnseignantRepository
{
    public function matieresParEnseignant(int $enseignantId)
    {
        /** @var User $enseignant */
        $enseignant = User::where('id', $enseignantId) ->where('role_id', User::ENSEIGNANT) ->firstOrFail();
        return $enseignant->matieres()->orderBy('libelle')->get();
    }

    public function syncMatieresParEnseignant(int $enseignantId, array $matiereIds): array
    {
        /** @var User $enseignant */
        $enseignant = User::where('id', $enseignantId)->where('role_id', User::ENSEIGNANT)->firstOrFail();
        $matiereIds = array_map('intval', $matiereIds);
        $validIds = Matiere::whereIn('id', $matiereIds)->pluck('id')->toArray();
        $enseignant->matieres()->sync($validIds);
        return $enseignant->fresh()->matieres()->orderBy('libelle')->get()->toArray();
    }

    public function enseignantsParMatiere(int $matiereId)
    {
        /** @var Matiere $matiere */
        $matiere = Matiere::findOrFail($matiereId);
        return $matiere->enseignants()->orderBy('nom')->orderBy('prenom')->get();
    }

    public function syncEnseignantsParMatiere(int $matiereId, array $enseignantIds): array
    {
        /** @var Matiere $matiere */
        $matiere = Matiere::findOrFail($matiereId);

        $enseignantIds = array_map('intval', $enseignantIds);
        $validIds = User::whereIn('id', $enseignantIds)
            ->where('role_id', User::ENSEIGNANT)
            ->pluck('id')
            ->toArray();

        $matiere->enseignants()->sync($validIds);

        return $matiere->fresh()->enseignants()->orderBy('nom')->orderBy('prenom')->get()->toArray();
    }
}

<?php

namespace Modules\Pedagogie\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Pedagogie\Http\Requests\AffectationEnseignantRequest;
use Modules\Pedagogie\Repositories\AffectationEnseignantRepository;

class AffectationEnseignantController extends CoreController
{
    protected AffectationEnseignantRepository $repository;

    public function __construct(AffectationEnseignantRepository $repository)
    {
        $this->repository = $repository;
    }

    public function matieresParEnseignant(int $enseignantId): JsonResponse
    {
        $matieres = $this->repository->matieresParEnseignant($enseignantId);
        return $this->returnSuccess('Matières de l\'enseignant récupérées avec succès', $matieres);
    }

    public function assignerMatieresAEnseignant(AffectationEnseignantRequest $request, int $enseignantId): JsonResponse
    {
        $matiereIds = $request->validated()['matiere_ids'] ?? [];
        $data = $this->repository->syncMatieresParEnseignant($enseignantId, $matiereIds);

        return $this->returnSuccess('Affectation des matières enregistrée avec succès', $data);
    }

    public function enseignantsParMatiere(int $matiereId): JsonResponse
    {
        $enseignants = $this->repository->enseignantsParMatiere($matiereId);
        return $this->returnSuccess('Liste des enseignants de la matière', $enseignants);
    }

    public function assignerEnseignantsAMatiere(AffectationEnseignantRequest $request, int $matiereId): JsonResponse
    {
        $enseignantIds = $request->validated()['enseignant_ids'] ?? [];
        $data = $this->repository->syncEnseignantsParMatiere($matiereId, $enseignantIds);

        return $this->returnSuccess('Affectation des enseignants enregistrée avec succès', $data);
    }
}

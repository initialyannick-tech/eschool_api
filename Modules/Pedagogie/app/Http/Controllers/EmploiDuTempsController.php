<?php

namespace Modules\Pedagogie\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Pedagogie\Http\Requests\EmploiDuTempsRequest;
use Modules\Pedagogie\Repositories\EmploiDuTempsRepository;
use Modules\Pedagogie\Services\EmploiTempsService;
use Modules\Pedagogie\Transformers\EmploiDuTempsResource;

class EmploiDuTempsController extends Controller
{
    protected EmploiDuTempsRepository $repository;
    protected EmploiTempsService $emploiTempsService;

    public function __construct(
        EmploiDuTempsRepository $repository,
        EmploiTempsService $emploiTempsService
    ) {
        $this->repository = $repository;
        $this->emploiTempsService = $emploiTempsService;
    }

    /**
     * Récupérer l'emploi du temps d'une classe.
     */
    public function getByClasse(Request $request, int $classeId): JsonResponse
    {
        $anneeScolaireId = $request->query('annee_scolaire_id');

        if (!$anneeScolaireId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'L\'ID de l\'année scolaire est requis.',
            ], 400);
        }

        $emplois = $this->repository->getByClasse($classeId, (int) $anneeScolaireId);

        return response()->json([
            'status' => 'success',
            'data'   => EmploiDuTempsResource::collection($emplois),
        ]);
    }

    /**
     * Récupérer l'emploi du temps d'un enseignant.
     */
    public function getByEnseignant(Request $request, int $enseignantId): JsonResponse
    {
        $anneeScolaireId = $request->query('annee_scolaire_id');

        if (!$anneeScolaireId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'L\'ID de l\'année scolaire est requis.',
            ], 400);
        }

        $emplois = $this->repository->getByEnseignant($enseignantId, (int) $anneeScolaireId);

        return response()->json([
            'status' => 'success',
            'data'   => EmploiDuTempsResource::collection($emplois),
        ]);
    }

    /**
     * Créer une séance avec détection préalable de conflit.
     */
    public function store(EmploiDuTempsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // 1. Détection automatique de conflit
        $conflits = $this->emploiTempsService->detecterConflits($validated);

        if (!empty($conflits)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Impossible de programmer ce cours en raison de conflits.',
                'errors'  => $conflits,
            ], 422);
        }

        // 2. Création de la séance
        $emploi = $this->repository->create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Séance ajoutée avec succès.',
            'data'    => new EmploiDuTempsResource($emploi->load(['classe', 'matiere', 'enseignant', 'salle'])),
        ], 201);
    }

    /**
     * Mettre à jour une séance avec détection de conflit.
     */
    public function update(EmploiDuTempsRequest $request, int $id): JsonResponse
    {
        $emploi = $this->repository->findById($id);

        if (!$emploi) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Créneau non trouvé.',
            ], 404);
        }

        $validated = $request->validated();

        // Détection de conflit en ignorant la séance actuelle ($id)
        $conflits = $this->emploiTempsService->detecterConflits($validated, $id);

        if (!empty($conflits)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Impossible de modifier ce cours en raison de conflits.',
                'errors'  => $conflits,
            ], 422);
        }

        $this->repository->update($emploi, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Séance mise à jour avec succès.',
            'data'    => new EmploiDuTempsResource($emploi->fresh(['classe', 'matiere', 'enseignant', 'salle'])),
        ]);
    }

    /**
     * Supprimer une séance.
     */
    public function destroy(int $id): JsonResponse
    {
        $emploi = $this->repository->findById($id);

        if (!$emploi) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Créneau non trouvé.',
            ], 404);
        }

        $this->repository->delete($emploi);

        return response()->json([
            'status'  => 'success',
            'message' => 'Séance supprimée avec succès.',
        ]);
    }
}
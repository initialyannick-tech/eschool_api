<?php

namespace Modules\Pedagogie\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Pedagogie\app\Http\Requests\SalleRequest;
use Modules\Pedagogie\app\Repositories\SalleRepository;
use Modules\Pedagogie\app\Transformers\SalleResource;

class SalleController extends Controller
{
    protected SalleRepository $salleRepository;

    public function __construct(SalleRepository $salleRepository)
    {
        $this->salleRepository = $salleRepository;
    }

    public function index(): JsonResponse
    {
        $salles = $this->salleRepository->getAll();

        return response()->json([
            'status' => 'success',
            'data'   => SalleResource::collection($salles),
        ]);
    }

    public function store(SalleRequest $request): JsonResponse
    {
        $salle = $this->salleRepository->create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Salle créée avec succès.',
            'data'    => new SalleResource($salle),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $salle = $this->salleRepository->findById($id);

        if (!$salle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Salle non trouvée.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => new SalleResource($salle),
        ]);
    }

    public function update(SalleRequest $request, int $id): JsonResponse
    {
        $salle = $this->salleRepository->findById($id);

        if (!$salle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Salle non trouvée.',
            ], 404);
        }

        $this->salleRepository->update($salle, $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Salle mise à jour avec succès.',
            'data'    => new SalleResource($salle),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $salle = $this->salleRepository->findById($id);

        if (!$salle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Salle non trouvée.',
            ], 404);
        }

        $this->salleRepository->delete($salle);

        return response()->json([
            'status'  => 'success',
            'message' => 'Salle supprimée avec succès.',
        ]);
    }
}
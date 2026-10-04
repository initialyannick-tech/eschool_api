<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Admin\Models\User;
use Modules\Pedagogie\Models\Matiere;
use Modules\Pedagogie\Transformers\MatiereResource;

class MatiereRepository
{
    private function relations(): array
    {
        return ['enseignants'];
    }

    public function index(): AnonymousResourceCollection
    {
        $matieres = Matiere::orderBy('libelle')->with($this->relations())->get();
        return MatiereResource::collection($matieres);
    }

    public function paginate(): AnonymousResourceCollection
    {
        $matieres = Matiere::orderBy('libelle')->with($this->relations())->paginate(10);
        return MatiereResource::collection($matieres);
    }

    public function show(int $id): ?Matiere
    {
        return Matiere::with($this->relations())->find($id);
    }

    /**
     * Création d'une matière
     *
     * @param [type] $data
     * @return false|Matiere
     */
    public function store($data)
    {
        $matiere = new Matiere;
        $matiere->fill($data);
        if($matiere->save()){
            return $matiere;
        }
        return false;
    }


    public function update(array $data, int $id)
    {
        /** @var Matiere|null $matiere */
        $matiere = Matiere::find($id);
        if (!$matiere) {
            return false;
        }

        if (isset($data['code'])) {
            $exists = Matiere::where('code', $data['code'])->where('id', '!=', $id)->exists();
            if ($exists) {
                return false;
            }
        }

        $matiere->fill($data);

        if ($matiere->save()) {
            return $matiere->fresh()->load($this->relations());
        }

        return false;
    }

    public function destroy(int $id): bool
    {
        /** @var Matiere|null $matiere */
        $matiere = Matiere::find($id);
        if (!$matiere) {
            return false;
        }

        $matiere->enseignants()->detach();
        return $matiere->delete();
    }

    public function assignerEnseignants(int $matiereId, array $enseignantIds): bool
    {
        $matiere = Matiere::findOrFail($matiereId);

        $enseignantIds = array_map('intval', $enseignantIds);
        $validIds = User::whereIn('id', $enseignantIds)
            ->where('role_id', User::ENSEIGNANT)
            ->pluck('id')
            ->toArray();

        $matiere->enseignants()->sync($validIds);

        return true;
    }

    public function enseignants(int $matiereId)
    {
        $matiere = Matiere::with('enseignants')->findOrFail($matiereId);
        return $matiere->enseignants;
    }
}

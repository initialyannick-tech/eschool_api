<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Pedagogie\Models\Eleve;
use Modules\Pedagogie\Models\ParentModel;
use Modules\Pedagogie\Transformers\EleveResource;

class EleveRepository
{
    /**
     * Liste complète des élèves
     */
    public function index(): AnonymousResourceCollection
    {
        $eleves = Eleve::with('parent')->orderBy('nom')->orderBy('prenom')->get();
        return EleveResource::collection($eleves);
    }

    /**
     * Liste paginée des élèves
     */
    public function paginate(): AnonymousResourceCollection
    {
        $eleves = Eleve::with('parent')->orderBy('nom')->orderBy('prenom')->paginate(15);
        return EleveResource::collection($eleves);
    }

    /**
     * Afficher un élève
     */
    public function show(int $id): EleveResource
    {
        $eleve = Eleve::with('parent')->findOrFail($id);
        return new EleveResource($eleve);
    }

    /**
     * Créer un élève
     */
    public function store(array $data): Eleve
    {
         //Le matricule est généré automatiquement.
        unset($data['matricule']);
        $data['matricule'] = $this->generateMatricule();
        return Eleve::create($data);
    }

    /**
     * Modifier un élève
     */
    public function update(array $data, int $id): Eleve {
        $eleve = Eleve::findOrFail($id);
        //Le matricule est permanent. Il ne peut donc pas être modifié.
        unset($data['matricule']);
        $eleve->update($data);
        return $eleve->fresh(['parent']);
    }

    /**
     * Supprimer un élève
     */
    public function destroy(int $id): bool
    {
        $eleve = Eleve::findOrFail($id);
        return $eleve->delete();
    }

    /**
     * Générer automatiquement le matricule
     *
     * Exemple :
     * ELE-000001
     * ELE-000002
     * ELE-000003
     */
    private function generateMatricule(): string
    {
        $lastEleve = Eleve::query()->orderByDesc('id')->first();
        $nextId = $lastEleve ? $lastEleve->id + 1 : 1;
        return 'ELE-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Assigner un élève à un parent
     */
    public function assignerParent(int $eleveId, int $parentId): Eleve {
        ParentModel::findOrFail($parentId);
        $eleve = Eleve::findOrFail($eleveId);
        $eleve->update(['parent_id' => $parentId,]);
        return $eleve->fresh(['parent']);
    }

    /**
     * Retirer le parent d'un élève
     */
    public function retirerParent(int $eleveId): Eleve {
        $eleve = Eleve::findOrFail($eleveId);
        $eleve->update(['parent_id' => null,]);
        return $eleve->fresh(['parent']);
    }

    /**
     * Liste des élèves appartenant à un parent
     */
    public function byParent(int $parentId): AnonymousResourceCollection {

        ParentModel::findOrFail($parentId);
        $eleves = Eleve::with('parent')
            ->where('parent_id', $parentId)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        return EleveResource::collection($eleves);
    }
}

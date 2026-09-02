<?php

namespace Modules\Pedagogie\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Pedagogie\Models\Classe;
use Modules\Pedagogie\Transformers\ClasseResource;

class ClasseRepository
{
    private function relations(): array
    {
        return [
            'anneeScolaire',
            'cycle',
            'serie',
            'professeurPrincipal',
            //'inscriptions.eleve',
        ];
    }

    /**
     * Liste sans pagination.
     */
    public function index(): AnonymousResourceCollection
    {
        $classes = Classe::orderBy('id', 'desc')->with($this->relations())->get();
        return ClasseResource::collection($classes);
    }

    /**
     * Liste paginée.
     */
    public function paginate(): AnonymousResourceCollection
    {
        $classes = Classe::orderBy('id', 'desc')->with($this->relations())->paginate(10);
        return ClasseResource::collection($classes);
    }

    /**
     * Afficher une classe.
     */
    public function show(int $id): ?Classe
    {
        return Classe::whereId($id)->with($this->relations())->first();
    }

    /**
     * Créer une classe.
     */
    public function store(array $data)
    {
        $data['actif'] = $data['actif'] ?? true;
        /**
         * Éviter les doublons.
         */
        $exists = Classe::where('annee_scolaire_id', $data['annee_scolaire_id'])->where('nom', $data['nom'])->exists();
        if ($exists) {
            return false;
        }
        $classe = new Classe();
        $classe->fill($data);
        if ($classe->save()) {
            return $classe->fresh()->load($this->relations());
        }
        return false;
    }

    /**
     * Modifier une classe.
     */
    public function update(array $data, int $id)
    {
        $classe = Classe::find($id);
        if (!$classe) {
            return false;
        }
        /**
         * L'année scolaire ne doit pas changer.
         */
        unset($data['annee_scolaire_id']);
        /**
         * Vérification du nom.
         */
        if (isset($data['nom'])) {

            $exists = Classe::where('annee_scolaire_id', $classe->annee_scolaire_id)->where('nom', $data['nom'])->where('id', '!=', $id)->exists();
            if ($exists) {
                return false;
            }
        }
        $classe->fill($data);
        if ($classe->save()) {
            return $classe
                ->fresh()
                ->load($this->relations());
        }
        return false;
    }

    /**
     * Supprimer une classe.
     */
    public function destroy(int $id): bool
    {
        $classe = Classe::find($id);
        if (!$classe) {
            return false;
        }
        /**
         * Une classe ayant des inscriptions
         * ne doit pas être supprimée.
         */
        if ($classe->inscriptions()->exists()) {
            return false;
        }
        return $classe->delete();
    }
}

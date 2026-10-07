<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentDossierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent' => ['required', 'array'],
            'parent.nom' => ['required', 'string', 'max:255'],
            'parent.prenom' => ['required', 'string', 'max:255'],
            'parent.telephone' => ['required', 'string', 'max:30'],
            'parent.telephone_secondaire' => ['nullable', 'string', 'max:30'],
            'parent.email' => ['nullable', 'email', 'max:255'],
            'parent.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'parent.adresse' => ['nullable', 'string', 'max:255'],
            'parent.profession' => ['nullable', 'string', 'max:255'],
            'parent.lieu_travail' => ['nullable', 'string', 'max:255'],
            'parent.statut' => ['sometimes', 'in:actif,inactif'],
            'parent.observation' => ['nullable', 'string'],
            'parent.relation' => ['required', 'string', 'max:50'],
            'parent.responsable_principal' => ['sometimes', 'boolean'],
            'parent.responsable_financier' => ['sometimes', 'boolean'],
            'eleve' => ['required', 'array'],
            'eleve.nom' => ['required', 'string', 'max:100'],
            'eleve.prenom' => ['required', 'string', 'max:100'],
            'eleve.sexe' => ['required', 'in:masculin,feminin'],
            'eleve.date_naissance' => ['required', 'date'],
            'eleve.lieu_naissance' => ['required', 'string', 'max:150'],
            'eleve.nationalite' => ['nullable', 'string', 'max:100'],
            'eleve.adresse' => ['nullable', 'string'],
            'eleve.telephone' => ['nullable', 'string', 'max:30'],
            'eleve.email' => ['nullable', 'email', 'max:255'],
            'eleve.photo' => ['nullable', 'string', 'max:255'],
            'eleve.situation_particuliere' => ['nullable', 'string'],
            'eleve.statut' => ['nullable', 'in:preinscrit,inscrit,reinscrit,transfere,suspendu,exclu,orienté,reorienté'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

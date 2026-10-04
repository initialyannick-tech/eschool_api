<?php

namespace Modules\Pedagogie\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmploiDuTempsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'classe_id'         => 'required|exists:classes,id',
            'matiere_id'        => 'required|exists:matieres,id',
            'enseignant_id'     => 'required|exists:users,id',
            'salle_id'          => 'nullable|exists:salles,id',
            'jour_semaine'      => 'required|in:lundi,mardi,mercredi,jeudi,vendredi,samedi',
            'heure_debut'       => 'required|date_format:H:i',
            'heure_fin'         => 'required|date_format:H:i|after:heure_debut',
            'type_cours'        => 'nullable|string|in:CM,TD,TP',
            'actif'             => 'nullable|boolean',
        ];
    }
}
<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EleveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:parents,id',],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:masculin,feminin'],
            'date_naissance' => ['required', 'date'],
            'lieu_naissance' => ['required', 'string', 'max:150'],
            'nationalite' => ['nullable', 'string', 'max:100'],
            'adresse' => ['nullable', 'string'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => ['nullable', 'string', 'max:255'],
            'situation_particuliere' => ['nullable', 'string'],
            'statut' => ['nullable', 'in:preinscrit,inscrit,reinscrit,transfere,suspendu,exclu,orienté,reorienté'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'parent_id.integer' => 'Le parent/tuteur sélectionné est invalide.',
            'parent_id.exists' => 'Le parent/tuteur sélectionné n’existe pas.',
            'nom.required' => 'Le nom de l’élève est obligatoire.',
            'nom.string' => 'Le nom de l’élève doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de l’élève ne peut pas dépasser 100 caractères.',
            'prenom.required' => 'Le prénom de l’élève est obligatoire.',
            'prenom.string' => 'Le prénom de l’élève doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom de l’élève ne peut pas dépasser 100 caractères.',
            'sexe.required' => 'Le sexe de l’élève est obligatoire.',
            'sexe.in' => 'Le sexe sélectionné est invalide.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance est invalide.',
            'lieu_naissance.required' =>'Le lieu de naissance est obligatoire.',
            'lieu_naissance.max' => 'Le lieu de naissance ne peut pas dépasser 150 caractères.',
            'nationalite.max' => 'La nationalité ne peut pas dépasser 100 caractères.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'email.email' => 'L’adresse email de l’élève est invalide.',
            'email.max' => 'L’adresse email ne peut pas dépasser 255 caractères.',
            'photo.max' => 'Le chemin de la photo ne peut pas dépasser 255 caractères.',
            'statut.in' => 'Le statut de l’élève est invalide.',
        ];
    }
}

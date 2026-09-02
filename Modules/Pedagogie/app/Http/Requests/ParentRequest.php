<?php

namespace Modules\Pedagogie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100',],
            'prenom' => ['required', 'string', 'max:100',],
            'relation' => ['required', 'string', 'max:50',],
            'telephone' => ['required', 'string', 'max:30',],
            'email' => ['nullable', 'email', 'max:255',],
            'adresse' => ['nullable', 'string',],
            'responsable_principal' => ['nullable', 'boolean',],
            'responsable_financier' => ['nullable', 'boolean',],
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
            'nom.required' => 'Le nom du parent/tuteur est obligatoire.',
            'nom.string' => 'Le nom du parent/tuteur doit être une chaîne de caractères.',
            'nom.max' => 'Le nom du parent/tuteur ne peut pas dépasser 100 caractères.',
            'prenom.required' => 'Le prénom du parent/tuteur est obligatoire.',
            'prenom.string' => 'Le prénom du parent/tuteur doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom du parent/tuteur ne peut pas dépasser 100 caractères.',
            'relation.required' => 'La relation avec l’élève est obligatoire.',
            'relation.max' => 'La relation ne peut pas dépasser 50 caractères.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'email.email' => 'L’adresse email est invalide.',
            'email.max' => 'L’adresse email ne peut pas dépasser 255 caractères.',
            'responsable_principal.boolean' =>'Le responsable principal doit être un booléen.',
            'responsable_financier.boolean' =>'Le responsable financier doit être un booléen.',
        ];
    }
}

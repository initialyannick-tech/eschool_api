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
            'nom' => [ 'required','string', 'max:255',],
            'prenom' => ['required','string','max:255',],
            'telephone' => ['required','string','max:30',],
            'telephone_secondaire' => ['nullable','string','max:30',],
            'email' => ['nullable','email','max:255',],
            'adresse' => ['nullable','string','max:255',],
            'profession' => ['nullable','string','max:255',],
            'lieu_travail' => ['nullable','string','max:255',],
            'statut' => [ 'required','in:actif,inactif',],
            'observation' => ['nullable','string',],
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
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string' => 'Le numéro de téléphone doit être valide.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 30 caractères.',
            'telephone_secondaire.string' => 'Le téléphone secondaire doit être valide.',
            'telephone_secondaire.max' => 'Le téléphone secondaire ne peut pas dépasser 30 caractères.',
            'email.email' => 'L’adresse email doit être valide.',
            'email.max' => 'L’adresse email ne peut pas dépasser 255 caractères.',
            'adresse.string' => 'L’adresse doit être une chaîne de caractères.',
            'adresse.max' => 'L’adresse ne peut pas dépasser 255 caractères.',
            'profession.string' => 'La profession doit être une chaîne de caractères.',
            'profession.max' => 'La profession ne peut pas dépasser 255 caractères.',
            'lieu_travail.string' => 'Le lieu de travail doit être une chaîne de caractères.',
            'lieu_travail.max' => 'Le lieu de travail ne peut pas dépasser 255 caractères.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné est invalide.',
            'observation.string' => 'L’observation doit être une chaîne de caractères.',
        ];
    }
}

<?php

namespace Modules\Parametre\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnneeScolaireRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'annee_debut' => ['required', 'integer', 'min:2000', 'max:2100',],
            'annee_fin' => ['required', 'integer', 'min:2001', 'max:2101', 'different:annee_debut',],
            'description' => ['nullable', 'string',],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Messages de validation.
     */
    public function messages(): array
    {
        return [
            'annee_debut.required' => 'L’année de début est obligatoire.',
            'annee_debut.integer' => 'L’année de début doit être un nombre entier.',
            'annee_debut.min' => 'L’année de début doit être supérieure ou égale à 2000.',
            'annee_debut.max' => 'L’année de début ne peut pas être supérieure à 2100.',
            'annee_fin.required' => 'L’année de fin est obligatoire.',
            'annee_fin.integer' => 'L’année de fin doit être un nombre entier.',
            'annee_fin.min' => 'L’année de fin doit être supérieure ou égale à 2001.',
            'annee_fin.max' => 'L’année de fin ne peut pas être supérieure à 2101.',
            'annee_fin.different' => 'L’année de fin doit être différente de l’année de début.',
            'description.string' => 'La description doit être une chaîne de caractères.',
        ];
    }

    /**
     * Validation supplémentaire.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($this->annee_debut && $this->annee_fin && $this->annee_fin !== ($this->annee_debut + 1)) {
                $validator->errors()->add(
                    'annee_fin',
                    'L’année scolaire doit couvrir deux années consécutives.'
                );
            }
        });
    }
}

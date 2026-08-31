<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('post');
        return [
            'nom'=> 'required|string',
            'prenom'=> 'required|string',
            'email'=> [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user)
            ],
            'role_id'=> 'required|exists:roles,id',
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
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prenom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse email valide',
            'email.unique' => 'L\'email est déjà utilisé',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'role_id.required' => 'Le role est obligatoire',
            'role_id.exists' => 'Le role n\'existe pas',
        ];
    }
}

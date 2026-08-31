<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
            'user_id' => 'required|exists:users,id',
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
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire',
            'user_id.required' => 'L\'utilisateur est obligatoire',
            'user_id.exists' => 'L\'utilisateur n\'existe pas',
        ];
    }
}

<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'libelle'=> 'required|string',
            'description'=> 'required|string',
            'permissions'=> 'required|array|min:1',
            'permissions.*'=> 'required|exists:permissions,id',
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
            'libelle.required' => 'Le libelle est obligatoire',
            'description.required' => 'La description est obligatoire',
            'permissions.required' => 'Les permissions sont obligatoires',
            'permissions.array' => 'Les permissions doivent être un tableau',
            'permissions.min' => 'Il faut au moins une permission',
            'permissions.*.required' => 'La permission est obligatoire',
            'permissions.*.exists' => 'La permission n\'existe pas',
        ];
    }
}

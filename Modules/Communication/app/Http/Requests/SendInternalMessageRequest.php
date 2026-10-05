<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Modules\Admin\Models\User;

class SendInternalMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'recipient_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('status', User::ACTIVE),
            ],
            'subject' => ['nullable', 'string', 'max:160'],
            'body' => ['nullable', 'string', 'max:10000', 'required_without:attachment'],
            'attachment' => [
                'nullable',
                'file',
                File::types(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'])->max('10mb'),
                'required_without:body',
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->permissions()
            ->whereIn('code', ['message.envoyer', 'messagerie.management'])
            ->exists() ?? false;
    }
}

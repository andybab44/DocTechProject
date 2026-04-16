<?php

namespace App\Http\Requests\Admin;

use App\Enums\Module;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id'    => ['required', 'integer', Rule::exists('users', 'id'), Rule::unique('licenses', 'user_id')],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'modules'    => ['nullable', 'array'],
            'modules.*'  => [Rule::enum(Module::class)],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id'=> ['required', 'integer', 'exists:inventory_items,id'],
            'items.*.quantity'         => ['required', 'integer', 'min:1'],
        ];
    }
}

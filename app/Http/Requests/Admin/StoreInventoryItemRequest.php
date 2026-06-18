<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:2000'],
            'quantity'            => ['required', 'integer', 'min:0'],
            'unit'                => ['required', 'string', 'max:50'],
            'category'            => ['nullable', 'string', 'max:100'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'vendor_id'           => ['nullable', 'integer', 'exists:vendors,id'],
        ];
    }
}

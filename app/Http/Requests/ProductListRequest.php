<?php

namespace App\Http\Requests;

use App\Models\ProductProperty;
use Illuminate\Foundation\Http\FormRequest;

class ProductListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'quantity_from' => 'nullable|integer',
            'quantity_to' => 'nullable|integer',
            'price_from' => 'nullable|float',
            'price_to' => 'nullable|float',

            ...$this->propertyRules(),
        ];
    }

    private function propertyRules(): array
    {
        $slugs = ProductProperty::pluck('slug')->toArray();

        $rules = [];
        foreach ($slugs as $slug) {
            $rules["properties.$slug"] = 'nullable|array';
            $rules["properties.$slug.*"] = 'string';
        }

        return $rules;
    }
}

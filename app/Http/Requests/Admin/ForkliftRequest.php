<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ForkliftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate it if you have roles
    }

    public function rules(): array
    {
        return [
        'name'          => ['required', 'string', 'max:120'],
        'hourly_rate'   => ['required', 'numeric', 'min:0'],
        'capacity_kg'   => ['required', 'numeric', 'min:1'],

        // 👈 NEW
        'location_name' => ['required', 'string', 'max:120'],

        // main image: required on create, optional on update
        'image'         => [
            $this->isMethod('post') ? 'required' : 'sometimes',
            'image',
            'max:5120',
        ],

        // gallery images
        'images.*'      => ['nullable', 'image', 'max:5120'],
    ];
    }
}

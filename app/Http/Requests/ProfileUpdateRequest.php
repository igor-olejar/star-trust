<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'url:https', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }
}

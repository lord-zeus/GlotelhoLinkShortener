<?php

namespace App\Http\Requests\Url;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'original_url' => 'required|url|max:2048',
            'custom_code' => 'nullable|alpha_dash|max:32|unique:short_urls,short_code',
            'short_code' => 'required|string|max:20|unique:urls,short_code',
            'expires_at' => 'nullable|date'
        ];
    }
}

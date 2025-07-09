<?php

namespace App\Http\Requests\Click;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url_id' => 'required|exists:urls,id',
            'visitor_ip' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:255',
            'referer' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'device_type' => 'nullable|string|max:100',
            'os' => 'nullable|string|max:100',
            'browser' => 'nullable|string|max:100',
            'screen_resolution' => 'nullable|string|max:50',
        ];
    }
}
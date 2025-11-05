<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'role_title' => ['required', 'string', 'max:150'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'file', 'image', 'max:4096'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.label' => ['nullable', 'string', 'max:80'],
            'social_links.*.url' => ['nullable', 'url', 'max:255'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_visible' => ['sometimes', 'boolean'],
        ];
    }
}

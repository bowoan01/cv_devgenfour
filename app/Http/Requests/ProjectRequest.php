<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('id') ?? $this->route('project');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'alpha_dash', 'max:180', Rule::unique('projects', 'slug')->ignore($projectId)],
            'client' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:120'],
            'tech_stack' => ['nullable'],
            'summary' => ['nullable', 'string'],
            'results' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'cover_image' => ['nullable', 'file', 'image', 'max:4096'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }
}

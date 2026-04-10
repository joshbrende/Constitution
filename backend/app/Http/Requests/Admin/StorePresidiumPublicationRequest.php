<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeUrlRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePresidiumPublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.section', 'presidium') ?? false;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:presidium_publications,slug'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'cover_url' => ['nullable', 'string', 'max:255', new SafeUrlRule()],
            'article_url' => ['nullable', 'string', 'max:255', new SafeUrlRule()],
            'purchase_url' => ['nullable', 'string', 'max:255', new SafeUrlRule()],
            'online_copy_url' => ['nullable', 'string', 'max:255', new SafeUrlRule()],
            'is_featured' => ['sometimes', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => (bool) $this->input('is_featured', false),
            'is_published' => (bool) $this->input('is_published', false),
            'order' => $this->input('order', 0),
        ]);
    }
}


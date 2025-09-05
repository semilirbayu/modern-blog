<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('post') ? $this->route('post')->id : null;
        $isCreating = $this->isMethod('post');

        return [
            'title' => $isCreating ? ['required', 'string', 'max:255'] : ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'content' => $isCreating ? ['required', 'string'] : ['sometimes', 'required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => $isCreating ? ['required', 'string', Rule::in(['draft', 'published'])] : ['sometimes', 'required', 'string', Rule::in(['draft', 'published'])],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.max' => 'The post title may not be greater than 255 characters.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'content.required' => 'The post content is required.',
            'excerpt.max' => 'The excerpt may not be greater than 500 characters.',
            'status.required' => 'The post status is required.',
            'status.in' => 'The status must be either draft or published.',
            'category_id.exists' => 'The selected category does not exist.',
            'published_at.date' => 'The published date must be a valid date.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // If slug is provided, ensure it's lowercase and properly formatted
        if ($this->has('slug') && !empty($this->slug)) {
            $this->merge([
                'slug' => str_replace(' ', '-', strtolower(trim($this->slug))),
            ]);
        }
    }
}
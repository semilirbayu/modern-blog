<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;
        $isCreating = $this->isMethod('post');

        return [
            'name' => $isCreating ? ['required', 'string', 'max:255'] : ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'description.max' => 'The description may not be greater than 1000 characters.',
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
<?php

namespace App\Http\Requests\Blogs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:200',
                Rule::unique('blogs', 'title')->ignore($this->route('blog')),
            ],
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'category_id' => ['nullable', 'exists:blog_categories,id'],
        ];
    }
}

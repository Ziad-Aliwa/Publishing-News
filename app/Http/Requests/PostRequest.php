<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->is('api/*') && $this->user()) {
            $this->merge(['post_creator' => $this->user()->getKey()]);
        }
    }

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
            'title' => ['required', 'min:3'],
            'description' => ['required', 'min:10'],
            'post_creator' => ['required', 'exists:users,id'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'title.min' => 'Title must be at least 3 characters',
            'description.min' => 'Description must be at least 10 characters',
            'description.required' => 'Description is required',
            'post_creator.required' => 'Post Creator is required',
            'post_creator.exists' => 'Post Creator not found in DB',
        ];
    }
}

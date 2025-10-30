<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => 'required|string|max:255',
            'body'            => 'required|string',
            'excerpt'         => 'nullable|string|max:300',
            'status'          => 'required|in:draft,published',
            'published_at'    => 'nullable|date',
            'cover'           => [
                'nullable',
                File::image()->max(5 * 1024),
                Rule::dimensions()->maxWidth(2000)->maxHeight(2000),
            ],
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:160',
            'category'         => 'nullable|string|max:100',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string|max:50',
        ];
    }
}

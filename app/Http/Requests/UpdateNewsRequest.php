<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('news.manage') ?? false; }

    public function rules(): array
    {
        return [
            'title'  => ['required','string','max:200'],
            'body'   => ['required','string'],
            'cover'  => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'status' => ['required','in:draft,published'],
            'published_at' => ['nullable','date'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:255'],
        ];
    }
}

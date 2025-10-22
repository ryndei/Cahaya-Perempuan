<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Rapikan input sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'            => trim((string) $this->input('title')),
            'category'         => trim((string) $this->input('category')),
            'reporter_name'    => trim((string) $this->input('reporter_name')),
            'reporter_address' => trim((string) $this->input('reporter_address')),
            // hilangkan spasi pada no HP, izinkan + di depan lewat aturan regex
            'reporter_phone'   => preg_replace('/\s+/', '', (string) $this->input('reporter_phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required','string','max:180'],
            'category'         => ['nullable','string','max:100'],
            'description'      => ['required','string','min:20'],

            // Kolom data pelapor (opsional)
            'reporter_name'    => ['nullable','string','max:120'],
            'reporter_address' => ['nullable','string','max:255'],
            'reporter_phone'   => ['nullable','string','max:30','regex:/^\+?\d{8,15}$/'],

            // Lampiran (maks 5MB)
            'attachment'       => ['nullable','file','mimes:jpg,jpeg,png,pdf,doc,docx,mp4','max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'reporter_phone.regex' => 'Format nomor telepon tidak valid. Gunakan 8–15 digit dan boleh diawali tanda +.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'            => 'judul',
            'category'         => 'kategori',
            'description'      => 'deskripsi',
            'reporter_name'    => 'nama',
            'reporter_address' => 'alamat',
            'reporter_phone'   => 'nomor telepon',
            'attachment'       => 'lampiran',
        ];
    }
}
